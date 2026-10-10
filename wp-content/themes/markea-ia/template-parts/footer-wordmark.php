<?php
/**
 * Footer wordmark, one clipped box per letter so each letter can slide in on scroll (motion/footer-logo.js).
 * Letters come from the Figma wordmark, kept at their original positions (--x and --w are in the 942.402 px artboard).
 *
 * @package Markea_IA
 */

defined( 'ABSPATH' ) || exit;
?>
<svg class="site-footer__defs" width="0" height="0" aria-hidden="true" focusable="false">
	<defs>
		<linearGradient id="markea-wordmark-fill" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="418.701">
			<stop stop-color="#fff"/>
			<stop offset="1" stop-color="#0049FF"/>
		</linearGradient>
	</defs>
</svg>
<div class="site-footer__wordmark" role="img" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<span class="site-footer__letter" style="--x:0;--w:167.96" data-footer-letter aria-hidden="true">
		<svg viewBox="0 0 167.96 128.471" focusable="false">
			<path d="M0 0V128.379H21.2409V44L61.9795 128.379H83.2205L21.2409 0H0Z" fill="url(#markea-wordmark-fill)"/>
			<path d="M84.6885 128.379H105.929L146.714 44V128.379H167.955V0H146.714L84.6885 128.379Z" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
	<span class="site-footer__letter" style="--x:196.35;--w:145.8" data-footer-letter aria-hidden="true">
		<svg viewBox="196.35 0 145.8 128.471" focusable="false">
			<path d="M279.575 0H258.884H258.334L196.354 128.379H217.595L269.253 21.45L320.91 128.379H342.151L280.125 0H279.575Z" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
	<span class="site-footer__letter" style="--x:353.8;--w:129.14" data-footer-letter aria-hidden="true">
		<svg viewBox="353.8 0 129.14 128.471" focusable="false">
			<path d="M482.947 35.4292C482.947 5.5 466.11 0.0458336 431.702 0.0458336H353.803V128.425H374.631V99.7792L374.494 75.1667H429.133C431.84 75.3042 434.363 75.35 436.795 75.35L461.66 128.471H482.901L456.338 73.4708C482.855 67.6042 482.901 49.0417 482.901 35.475M429.179 56.0083H374.677V19.1125H429.454C453.127 19.1125 461.109 20.9 461.109 35.75C461.109 50.6 458.448 55.9625 429.225 55.9625" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
	<span class="site-footer__letter" style="--x:502.81;--w:138.69" data-footer-letter aria-hidden="true">
		<svg viewBox="502.81 0 138.69 128.471" focusable="false">
			<path d="M628.605 0H607.41L523.685 107.479V93.6375V72.9667V0H502.811V128.379H507.399H523.685H528.64L585.206 55.7792L620.256 128.379H641.497L598.281 38.9583L628.605 0Z" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
	<span class="site-footer__letter" style="--x:665.35;--w:118.55" data-footer-letter aria-hidden="true">
		<svg viewBox="665.35 0 118.55 128.471" focusable="false">
			<path d="M686.227 75.1208V74.4792H766.832V53.9H686.227V19.1125H783.164V0H665.353V128.379H783.898V109.267H686.227V75.1208Z" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
	<span class="site-footer__letter" style="--x:797.16;--w:145.24" data-footer-letter aria-hidden="true">
		<svg viewBox="797.16 0 145.24 128.471" focusable="false">
			<path d="M880.423 0H880.377H859.182H859.136V0.0458333L797.157 128.379H818.398L869.78 22.0458L921.161 128.379H942.402L880.377 0.0458333L880.423 0Z" fill="url(#markea-wordmark-fill)"/>
		</svg>
	</span>
</div>
