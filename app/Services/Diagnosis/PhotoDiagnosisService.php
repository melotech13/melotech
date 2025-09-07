<?php

namespace App\Services\Diagnosis;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PhotoDiagnosisService
{
	/**
	 * Analyze an uploaded photo and return a diagnosis.
	 */
	public function analyze(UploadedFile $photo, string $analysisType): array
	{
		$start = microtime(true);
		$this->validateInput($photo, $analysisType);

		$meta = $this->extractImageMetadata($photo);
		list($image, $width, $height) = $this->loadAndNormalizeImage($photo);

		try {
			$stats = $this->sampleImageStatistics($image, $width, $height);
			$diagnosis = $this->determineDiagnosis($analysisType, $stats);

			$result = [
				'identified_type' => $diagnosis['label'],
				'confidence_score' => $diagnosis['confidence'],
				'recommendations' => $this->buildRecommendations($analysisType, $diagnosis['label']),
				'analysis_details' => array_merge($stats, [
					'analysis_method' => 'gd_systematic_sampling_v1'
				]),
				'processing_time' => round((microtime(true) - $start) * 1000, 2),
				'image_metadata' => $meta
			];

			return $result;
		} finally {
			if (\is_resource($image) || (class_exists('GdImage') && $image instanceof \GdImage)) {
				imagedestroy($image);
			}
		}
	}

	private function validateInput(UploadedFile $photo, string $analysisType): void
	{
		if (!$photo->isValid()) {
			throw new \InvalidArgumentException('Invalid photo file');
		}
		if (!\in_array($analysisType, ['leaves', 'watermelon'], true)) {
			throw new \InvalidArgumentException('Unsupported analysis type');
		}
	}

	private function extractImageMetadata(UploadedFile $photo): array
	{
		$path = $photo->getRealPath();
		$info = @getimagesize($path) ?: [0, 0, null, 'mime' => $photo->getMimeType()];
		return [
			'width' => $info[0] ?? 0,
			'height' => $info[1] ?? 0,
			'mime_type' => $info['mime'] ?? $photo->getMimeType(),
			'file_size' => $photo->getSize()
		];
	}

	/**
	 * Load image and normalize to max dimension for consistent analysis.
	 */
	private function loadAndNormalizeImage(UploadedFile $photo): array
	{
		$path = $photo->getRealPath();
		$info = getimagesize($path);
		if (!$info) {
			throw new \RuntimeException('Unable to read image');
		}
		switch ($info[2]) {
			case IMAGETYPE_JPEG:
				$img = imagecreatefromjpeg($path);
				break;
			case IMAGETYPE_PNG:
				$img = imagecreatefrompng($path);
				break;
			case IMAGETYPE_GIF:
				$img = imagecreatefromgif($path);
				break;
			default:
				throw new \InvalidArgumentException('Unsupported image type');
		}

		$w = imagesx($img);
		$h = imagesy($img);
		$maxDim = 768; // keep details for better spot detection
		if ($w > $maxDim || $h > $maxDim) {
			$scale = min($maxDim / $w, $maxDim / $h);
			$nw = max(1, (int)floor($w * $scale));
			$nh = max(1, (int)floor($h * $scale));
			$dst = imagecreatetruecolor($nw, $nh);
			imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
			imagedestroy($img);
			$img = $dst;
			$w = $nw;
			$h = $nh;
		}

		return [$img, $w, $h];
	}

	/**
	 * Systematic sampling to compute robust color/brightness statistics and spot index.
	 */
	private function sampleImageStatistics($image, int $width, int $height): array
	{
		$totalPixels = $width * $height;
		$targetSamples = min(4000, $totalPixels);
		$stepX = max(1, (int)floor($width / sqrt($targetSamples)));
		$stepY = max(1, (int)floor($height / sqrt($targetSamples)));

		$green = 0; $yellow = 0; $brown = 0; $dark = 0; $neutral = 0;
		$windowDarkHits = 0; $windowCount = 0;

		$windowSize = max(6, (int)floor(min($width, $height) / 80));
		$windowThreshold = 0.35; // window considered spotty if dark ratio > 35%

		for ($y = 0; $y < $height; $y += $stepY) {
			for ($x = 0; $x < $width; $x += $stepX) {
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF; $g = ($rgb >> 8) & 0xFF; $b = $rgb & 0xFF;
				$brightness = ($r + $g + $b) / 3.0;
				$maxC = max($r, $g, $b); $minC = min($r, $g, $b); $range = $maxC - $minC;
				$gDom = $g - max($r, $b); $rDom = $r - max($g, $b);

				if ($gDom > 18 && $g > 80 && $brightness > 55 && $range < 90) {
					$green++;
				} elseif ($g > 110 && $r > 115 && $b < 110 && $brightness > 85) {
					$yellow++;
				} elseif ($brightness < 45) {
					$dark++;
				} elseif (($r + $g) / 2 > $b + 25 && $g < 120 && $brightness < 85) {
					$brown++;
				} else {
					$neutral++;
				}

				// Local window darkness to detect spots distribution
				$wx0 = max(0, $x - $windowSize);
				$wy0 = max(0, $y - $windowSize);
				$wx1 = min($width - 1, $x + $windowSize);
				$wy1 = min($height - 1, $y + $windowSize);
				$wPixels = 0; $wDark = 0;
				for ($wy = $wy0; $wy <= $wy1; $wy += max(1, (int)floor(($windowSize) / 2))) {
					for ($wx = $wx0; $wx <= $wx1; $wx += max(1, (int)floor(($windowSize) / 2))) {
						$rgb2 = imagecolorat($image, $wx, $wy);
						$r2 = ($rgb2 >> 16) & 0xFF; $g2 = ($rgb2 >> 8) & 0xFF; $b2 = $rgb2 & 0xFF;
						$br2 = ($r2 + $g2 + $b2) / 3.0;
						if ($br2 < 45) { $wDark++; }
						$wPixels++;
					}
				}
				if ($wPixels > 0) {
					$windowCount++;
					if (($wDark / $wPixels) > $windowThreshold) {
						$windowDarkHits++;
					}
				}
			}
		}

		$sampled = $green + $yellow + $brown + $dark + $neutral;
		$spotIndex = $windowCount > 0 ? ($windowDarkHits / $windowCount) : 0.0;

		return [
			'green_ratio' => $sampled ? $green / $sampled : 0,
			'yellow_ratio' => $sampled ? $yellow / $sampled : 0,
			'brown_ratio' => $sampled ? $brown / $sampled : 0,
			'dark_ratio' => $sampled ? $dark / $sampled : 0,
			'neutral_ratio' => $sampled ? $neutral / $sampled : 0,
			'spot_index' => $spotIndex,
			'sampled_pixels' => $sampled,
			'grid_step_x' => $stepX,
			'grid_step_y' => $stepY
		];
	}

	private function determineDiagnosis(string $type, array $s): array
	{
		$green = $s['green_ratio'];
		$yellow = $s['yellow_ratio'];
		$brown = $s['brown_ratio'];
		$dark = $s['dark_ratio'];
		$spots = $s['spot_index'];
		$totalDark = $brown + $dark;

		if ($type === 'leaves') {
			// Priority: severe disease
			if (($spots > 0.22 && $totalDark > 0.28 && $green < 0.40) || $totalDark > 0.55) {
				return $this->result('Spotted/Diseased Leaves', $this->confidenceFrom($totalDark, 0.55, 0.8));
			}
			// Yellowing
			if ($yellow >= 0.28 && $totalDark < 0.35) {
				return $this->result('Yellowing Leaves', $this->confidenceFrom($yellow, 0.28, 0.6));
			}
			// Healthy if green dominates and darkness is limited
			if ($green >= 0.35 && $totalDark < 0.30) {
				return $this->result('Healthy Green Leaves', $this->confidenceFrom($green, 0.35, 0.85));
			}
			// Fallbacks leaning healthy if reasonable green present
			if ($green >= 0.22 && $totalDark < 0.45) {
				return $this->result('Healthy Green Leaves', 70);
			}
			return $this->result('Spotted/Diseased Leaves', 65);
		}

		// Watermelon heuristics
		if ($totalDark > 0.18 && $spots > 0.18) {
			return $this->result('Defective/Diseased Watermelon', $this->confidenceFrom($totalDark, 0.18, 0.75));
		}
		if ($yellow >= 0.25 && $green < 0.35) {
			return $this->result('Ripe Watermelon', $this->confidenceFrom($yellow, 0.25, 0.7));
		}
		if ($yellow >= 0.12 && $yellow < 0.25 && $green < 0.45) {
			return $this->result('Nearly Ripe Watermelon', $this->confidenceFrom($yellow, 0.12, 0.6));
		}
		if ($green >= 0.40) {
			return $this->result('Unripe Watermelon', $this->confidenceFrom($green, 0.40, 0.6));
		}
		return $this->result('Ripe Watermelon', 55);
	}

	private function result(string $label, int $confidence): array
	{
		return ['label' => $label, 'confidence' => $confidence];
	}

	private function confidenceFrom(float $value, float $threshold, float $maxConfidence): int
	{
		$excess = max(0.0, $value - $threshold);
		$score = min(1.0, $excess * 2.0); // scale up to 1.0
		$conf = (int)round(50 + $score * (100 * $maxConfidence - 50));
		return max(50, min(95, $conf));
	}

	private function buildRecommendations(string $type, string $condition): array
	{
		if ($type === 'leaves') {
			return [
				'condition' => strtolower(str_replace(' ', '_', $condition)),
				'condition_label' => $condition,
				'recommendations' => $this->leafRecommendations($condition),
				'urgency_level' => $this->urgencyLevel($type, $condition),
				'treatment_category' => $this->treatmentCategory($type, $condition)
			];
		}
		return [
			'condition' => strtolower(str_replace(' ', '_', $condition)),
			'condition_label' => $condition,
			'recommendations' => $this->watermelonRecommendations($condition),
			'urgency_level' => $this->urgencyLevel($type, $condition),
			'treatment_category' => $this->treatmentCategory($type, $condition)
		];
	}

	private function leafRecommendations(string $condition): array
	{
		switch ($condition) {
			case 'Healthy Green Leaves':
				return [
					"Water about 1–1.5 inches per week, split into two gentle waterings, and try to keep the leaves dry so diseases don’t spread.",
					"Feed with a balanced fertilizer (10-10-10) every 4–6 weeks at a light rate, which helps the plants stay strong without overgrowing.",
					"Add a 5–8 cm layer of mulch around the base to keep the soil moist and clean, and to stop dirty water from splashing onto the leaves.",
					"Check your plants once a week for any spots or insects, and remove fallen leaves or weeds so pests have fewer places to hide.",
					"Keep 2–3 feet of space between plants and trim crowded shoots a little, so fresh air can move through and keep leaves healthy."
				];
			case 'Yellowing Leaves':
				return [
					"Press your finger 3–5 cm into the soil; if it feels wet, hold off on watering until the top layer dries, which helps stop yellowing.",
					"Give a small dose of a nitrogen fertilizer and water it in well, as gentle feeding often brings back the leaf’s healthy green color.",
					"If your soil is too acidic, lightly add garden lime following the label, because leaves yellow when nutrients can’t move in poor pH.",
					"When veins look green but the spaces turn yellow, spray a chelated iron foliar feed as directed, and repeat after about a week if needed.",
					"Loosen hard or soggy soil around the plant so roots can breathe better, and fix any areas where water tends to sit for long."
				];
			case 'Spotted/Diseased Leaves':
				return [
					"Cut off the spotted leaves and place them in a bag for the trash, because leaving them nearby can spread the problem.",
					"Spray a copper-based fungicide exactly as the label says and repeat after 7–10 days, which helps stop new spots from forming.",
					"Water at the base with a hose or drip line for the next two weeks, since keeping the leaves dry makes it much harder for disease to grow.",
					"Wipe your pruners or knife with alcohol before moving to the next plant, so you don’t accidentally carry the disease along the row.",
					"Trim thick, crowded growth and keep about 2–3 feet of space, giving the leaves more air and sunshine to dry quickly after rain."
				];
			default:
				return [
					"Take another photo outdoors in soft daylight and hold the camera 30–50 cm away, so the leaf details are clear and easy to read.",
					"Try watering at the soil level instead of over the top, because dry leaves make it harder for many common diseases to spread.",
					"Give a small, balanced feeding and check again in a week, since steady nutrition and patience usually bring the plant back on track."
				];
		}
	}

	private function watermelonRecommendations(string $condition): array
	{
		switch ($condition) {
			case 'Ripe Watermelon':
				return [
					"Pick in the cool morning so the fruit stays firm, and keep it out of direct sun after cutting to protect the sweet flesh.",
					"Tap the watermelon and listen for a deep hollow thump, and look for a creamy yellow ground spot, which are good signs it is ready.",
					"Cut the stem a little above the fruit using clean pruners, and carry it gently to avoid bruising the rind and inside.",
					"Let it rest in a shaded, airy place for a short while before storage, and keep it fairly cool so it stays tasty longer."
				];
			case 'Nearly Ripe Watermelon':
				return [
					"Give the fruit a few more days and watch the nearest tendril; when it turns brown and dry, it’s usually time to harvest.",
					"Water a bit less this week so the sugars can build up inside, which often improves flavor and sweetness.",
					"If you can test one fruit, a sweetness reading of around 10 or higher is a good sign that the crop is ready to enjoy.",
					"Turn the fruit gently every couple of days so one side doesn’t get soft or develop a flat spot on the ground."
				];
			case 'Unripe Watermelon':
				return [
					"Keep watering steady each week and try not to let the soil swing from very dry to very wet, which can slow fruit growth.",
					"Feed lightly with a low‑nitrogen fertilizer every few weeks, because too much nitrogen grows leaves instead of sweet fruit.",
					"If many fruits set on one vine, remove a few so the remaining ones grow bigger and reach better sweetness.",
					"Rest each fruit on straw or cardboard so it stays clean and dry, which helps prevent rotting where it touches the soil."
				];
			case 'Defective/Diseased Watermelon':
				return [
					"Cut off and discard any damaged or rotting fruit right away, and don’t compost it, so the problem doesn’t spread further.",
					"Check the nearby vines within the next day or two for similar signs, since issues often travel down the row quickly.",
					"Clean your cutting tools with alcohol before moving to the next plant, which helps stop spreading disease from plant to plant.",
					"If a disease is confirmed, use a product that is allowed for your crop and follow the label closely for timing and safety."
				];
			default:
				return [
					"Take a new photo outside in soft daylight without flash, because even lighting helps show the fruit’s true color and condition.",
					"Try to include the whole fruit and the ground spot from about an arm’s length away, so the picture tells the full story.",
					"Keep watering steady for a few days and then check again, since consistent care often clears up small issues on its own."
				];
		}
	}

	private function urgencyLevel(string $type, string $condition): string
	{
		if ($type === 'leaves') {
			if (str_contains($condition, 'Spotted') || str_contains($condition, 'Diseased')) { return 'high'; }
			if (str_contains($condition, 'Yellow')) { return 'medium'; }
			return 'low';
		}
		if (str_contains($condition, 'Defective') || str_contains($condition, 'Diseased')) { return 'high'; }
		if (str_contains($condition, 'Nearly')) { return 'medium'; }
		return 'low';
	}

	private function treatmentCategory(string $type, string $condition): string
	{
		if ($type === 'leaves') {
			if (str_contains($condition, 'Spotted') || str_contains($condition, 'Diseased')) { return 'urgent_treatment'; }
			if (str_contains($condition, 'Yellow')) { return 'care'; }
			return 'maintenance';
		}
		if (str_contains($condition, 'Defective') || str_contains($condition, 'Diseased')) { return 'urgent_treatment'; }
		if (str_contains($condition, 'Ripe')) { return 'harvest'; }
		if (str_contains($condition, 'Nearly')) { return 'monitoring'; }
		return 'maintenance';
	}
}


