<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function flexblocks_get_dynamic_css( $block, $content = '' ) {
	/**
	 * Get our Grid block CSS.
	 *
	 * @since 0.1
	 */
	if ( 'grid' === $block ) {
		$data = flexblocks_get_block_data( 'flexblocks/grid', $content );

		if ( empty( $data ) ) {
			return;
		}

		$css = new FlexBlocks_Dynamic_CSS;

		$css->set_selector( '.fx-grid-wrapper' );
		$css->add_property( 'display', 'flex' );
		$css->add_property( 'flex-wrap', 'wrap' );

		foreach ( $data as $atts ) {
			if ( ! isset( $atts['uniqueId'] ) ) {
				continue;
			}

			$defaults = flexblocks_get_block_defaults();

			$settings = wp_parse_args(
				$atts,
				$defaults['gridContainer']
			);

			$id = $atts['uniqueId'];

			$css->set_selector( '.fx-grid-wrapper-' . $id );
			$css->add_property( 'align-items', $settings['verticalAlignment'] );
			$css->add_property( 'justify-content', $settings['horizontalAlignment'] );

			if ( $settings['horizontalGap'] ) {
				$css->add_property( 'margin-left', '-' . $settings['horizontalGap'] . 'px' );
			}


			$css->set_selector( '.fx-grid-wrapper-' . $id . ' > .fx-grid-column' );

			if ( $settings['horizontalGap'] ) {
				$css->add_property( 'box-sizing', 'border-box' );
				$css->add_property( 'padding-left', $settings['horizontalGap'], 'px' );
			}

			$css->add_property( 'padding-bottom', $settings['verticalGap'], 'px' );

			$css->start_media_query( apply_filters( 'flexblocks_tablet_media_query', '(max-width: 1024px)' ) );
				$css->set_selector( '.fx-grid-wrapper-' . $id );

				if ( 'inherit' !== $settings['verticalAlignmentTablet'] ) {
					$css->add_property( 'align-items', $settings['verticalAlignmentTablet'] );
				}

				if ( 'inherit' !== $settings['horizontalAlignmentTablet'] ) {
					$css->add_property( 'justify-content', $settings['horizontalAlignmentTablet'] );
				}

				if ( $settings['horizontalGapTablet'] ) {
					$css->add_property( 'margin-left', '-' . $settings['horizontalGapTablet'] . 'px' );
				}

				$css->set_selector( '.fx-grid-wrapper-' . $id . ' > .fx-grid-column' );

				if ( $settings['horizontalGapTablet'] ) {
					$css->add_property( 'box-sizing', 'border-box' );
					$css->add_property( 'padding-left', $settings['horizontalGapTablet'], 'px' );
				}

				$css->add_property( 'padding-bottom', $settings['verticalGapTablet'], 'px' );
			$css->stop_media_query();

			$css->start_media_query( apply_filters( 'flexblocks_mobile_media_query', '(max-width:768px)' ) );
				$css->set_selector( '.fx-grid-wrapper-' . $id );

				if ( 'inherit' !== $settings['verticalAlignmentMobile'] ) {
					$css->add_property( 'align-items', $settings['verticalAlignmentMobile'] );
				}

				if ( 'inherit' !== $settings['horizontalAlignmentMobile'] ) {
					$css->add_property( 'justify-content', $settings['horizontalAlignmentMobile'] );
				}

				if ( $settings['horizontalGapMobile'] ) {
					$css->add_property( 'margin-left', '-' . $settings['horizontalGapMobile'] . 'px' );
				}

				$css->set_selector( '.fx-grid-wrapper-' . $id . ' > .fx-grid-column' );

				if ( $settings['horizontalGapMobile'] ) {
					$css->add_property( 'box-sizing', 'border-box' );
					$css->add_property( 'padding-left', $settings['horizontalGapMobile'], 'px' );
				}

				$css->add_property( 'padding-bottom', $settings['verticalGapMobile'], 'px' );
			$css->stop_media_query();
		}

		return $css->css_output();
	}

	/**
	 * Get our Container block CSS.
	 *
	 * @since 0.1
	 */
	if ( 'container' === $block ) {
		$data = flexblocks_get_block_data( 'flexblocks/container', $content );

		if ( empty( $data ) ) {
			return;
		}

		$css = new FlexBlocks_Dynamic_CSS;

		foreach ( $data as $atts ) {
			if ( ! isset( $atts['uniqueId'] ) ) {
				continue;
			}

			$defaults = flexblocks_get_block_defaults();

			$settings = wp_parse_args(
				$atts,
				$defaults['container']
			);

			$id = $atts['uniqueId'];

			// Open main container element.
			$css->set_selector( '.fx-container.fx-container-' . $id );

			if ( 'contained' === $settings['outerContainer'] ) {
				$css->add_property( 'max-width', absint( $settings['containerWidth'] ), 'px' );
				$css->add_property( 'margin-left', 'auto' );
				$css->add_property( 'margin-right', 'auto' );
			}

			$css->add_property( 'background-color', $settings['backgroundColor'] );
			$css->add_property( 'color', $settings['textColor'] );

			if ( $settings['bgImage'] ) {
				$url = $settings['bgImage']['image']['url'];

				if ( $settings['backgroundColor'] && isset( $settings['bgOptions']['overlay'] ) && $settings['bgOptions']['overlay'] ) {
					$css->add_property( 'background-image', 'linear-gradient(0deg, ' . $settings['backgroundColor'] . ', ' . $settings['backgroundColor'] . '), url(' . esc_url( $url ) . ')' );
				} else {
					$css->add_property( 'background-image', 'url(' . esc_url( $url ) . ')' );
				}

				$css->add_property( 'background-repeat', $settings['bgOptions']['repeat'] );
				$css->add_property( 'background-position', $settings['bgOptions']['position'] );
				$css->add_property( 'background-size', $settings['bgOptions']['size'] );
				$css->add_property( 'background-attachment', $settings['bgOptions']['attachment'] );
			}

			$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTop'], $settings['marginRight'], $settings['marginBottom'], $settings['marginLeft'], 'px' ) );

			if ( $settings['zindex'] ) {
				$css->add_property( 'position', 'relative' );
				$css->add_property( 'z-index', $settings['zindex'] );
			}

			$css->set_selector( '.fx-container.fx-container-' . $id . ' > .fx-inside-container' );
			$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTop'], $settings['paddingRight'], $settings['paddingBottom'], $settings['paddingLeft'], 'px' ) );

			if ( 'contained' === $settings['innerContainer'] && ! $settings['isGrid'] ) {
				$css->add_property( 'max-width', absint( $settings['containerWidth'] ), 'px' );
				$css->add_property( 'margin-left', 'auto' );
				$css->add_property( 'margin-right', 'auto' );
			}

			$css->set_selector( '.fx-container.fx-container-' . $id . ' a, .fx-container.fx-container-' . $id . ' a:visited' );
			$css->add_property( 'color', $settings['linkColor'] );

			$css->set_selector( '.fx-container.fx-container-' . $id . ' a:hover' );
			$css->add_property( 'color', $settings['linkColorHover'] );

			$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id );
			$css->add_property( 'width', $settings['width'], '%' );

			if ( $settings['removeVerticalGap'] ) {
				$css->set_selector( '.fx-grid-wrapper > div.fx-grid-column-' . $id );
				$css->add_property( 'padding-bottom', '0px' );
			}

			$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id . ' > .fx-container' );
			$css->add_property( 'display', 'flex' );
			$css->add_property( 'flex-direction', 'column' );
			$css->add_property( 'height', '100%' );
			$css->add_property( 'justify-content', $settings['verticalAlignment'] );

			$css->start_media_query( apply_filters( 'flexblocks_tablet_media_query', '(max-width: 1024px)' ) );
				$css->set_selector( '.fx-container.fx-container-' . $id );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopTablet'], $settings['marginRightTablet'], $settings['marginBottomTablet'], $settings['marginLeftTablet'], 'px' ) );

				$css->set_selector( '.fx-container.fx-container-' . $id . ' > .fx-inside-container' );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopTablet'], $settings['paddingRightTablet'], $settings['paddingBottomTablet'], $settings['paddingLeftTablet'], 'px' ) );

				$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id );
				$css->add_property( 'width', $settings['widthTablet'], '%' );

				if ( $settings['removeVerticalGapTablet'] ) {
					$css->set_selector( '.fx-grid-wrapper > div.fx-grid-column-' . $id );
					$css->add_property( 'padding-bottom', '0px' );
				}

				$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id . ' > .fx-container' );

				if ( 'inherit' !== $settings['verticalAlignmentTablet'] ) {
					$css->add_property( 'justify-content', $settings['verticalAlignmentTablet'] );
				}
			$css->stop_media_query();

			$css->start_media_query( apply_filters( 'flexblocks_mobile_media_query', '(max-width:768px)' ) );
				$css->set_selector( '.fx-container.fx-container-' . $id );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopMobile'], $settings['marginRightMobile'], $settings['marginBottomMobile'], $settings['marginLeftMobile'], 'px' ) );

				$css->set_selector( '.fx-container.fx-container-' . $id . ' > .fx-inside-container' );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopMobile'], $settings['paddingRightMobile'], $settings['paddingBottomMobile'], $settings['paddingLeftMobile'], 'px' ) );

				$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id );
				$css->add_property( 'width', $settings['widthMobile'], '%' );

				if ( $settings['removeVerticalGapMobile'] ) {
					$css->set_selector( '.fx-grid-wrapper > div.fx-grid-column-' . $id );
					$css->add_property( 'padding-bottom', '0px' );
				}

				$css->set_selector( '.fx-grid-wrapper > .fx-grid-column-' . $id . ' > .fx-container' );

				if ( 'inherit' !== $settings['verticalAlignmentMobile'] ) {
					$css->add_property( 'justify-content', $settings['verticalAlignmentMobile'] );
				}
			$css->stop_media_query();
		}

		return $css->css_output();
	}

	/**
	 * Get our Button Container block CSS.
	 *
	 * @since 0.1
	 */
	if ( 'button-container' === $block ) {
		$data = flexblocks_get_block_data( 'flexblocks/button-container', $content );

		if ( empty( $data ) ) {
			return;
		}

		$css = new FlexBlocks_Dynamic_CSS;

		$css->set_selector( '.fx-button-wrapper' );
		$css->add_property( 'display', 'flex' );
		$css->add_property( 'flex-wrap', 'wrap' );
		$css->add_property( 'align-items', 'flex-start' );
		$css->add_property( 'justify-content', 'flex-start' );
		$css->add_property( 'clear', 'both' );

		foreach ( $data as $atts ) {
			if ( ! isset( $atts['uniqueId'] ) ) {
				continue;
			}

			$defaults = flexblocks_get_block_defaults();

			$settings = wp_parse_args(
				$atts,
				$defaults['buttonContainer']
			);

			$id = $atts['uniqueId'];

			$css->set_selector( '.fx-button-wrapper-' . $id );
			$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTop'], $settings['marginRight'], $settings['marginBottom'], $settings['marginLeft'], 'px' ) );
			$css->add_property( 'justify-content', 'right' === $settings['alignment'] ? 'flex-end' : $settings['alignment'] );

			if ( $settings['stack'] ) {
				$css->add_property( 'flex-direction', 'column' );
				$css->add_property( 'align-items', 'right' === $settings['alignment'] ? 'flex-end' : $settings['alignment'] );
			}

			$css->start_media_query( apply_filters( 'flexblocks_tablet_media_query', '(max-width: 1024px)' ) );
				$css->set_selector( '.fx-button-wrapper-' . $id );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopTablet'], $settings['marginRightTablet'], $settings['marginBottomTablet'], $settings['marginLeftTablet'], 'px' ) );
				$css->add_property( 'justify-content', 'right' === $settings['alignmentTablet'] ? 'flex-end' : $settings['alignmentTablet'] );

				if ( $settings['stackTablet'] ) {
					$css->add_property( 'flex-direction', 'column' );
					$css->add_property( 'align-items', 'right' === $settings['alignmentTablet'] ? 'flex-end' : $settings['alignmentTablet'] );
				}
			$css->stop_media_query();


			$css->start_media_query( apply_filters( 'flexblocks_mobile_media_query', '(max-width:768px)' ) );
				$css->set_selector( '.fx-button-wrapper-' . $id );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopMobile'], $settings['marginRightMobile'], $settings['marginBottomMobile'], $settings['marginLeftMobile'], 'px' ) );
				$css->add_property( 'justify-content', 'right' === $settings['alignmentMobile'] ? 'flex-end' : $settings['alignmentMobile'] );

				if ( $settings['stackMobile'] ) {
					$css->add_property( 'flex-direction', 'column' );
					$css->add_property( 'align-items', 'right' === $settings['alignmentMobile'] ? 'flex-end' : $settings['alignmentMobile'] );
				}
			$css->stop_media_query();
		}

		return $css->css_output();
	}

	/**
	 * Get our Button block CSS.
	 *
	 * @since 0.1
	 */
	if ( 'button' === $block ) {
		$data = flexblocks_get_block_data( 'flexblocks/button', $content );

		if ( empty( $data ) ) {
			return;
		}

		$css = new FlexBlocks_Dynamic_CSS;

		$css->set_selector( '.fx-button' );
		$css->add_property( 'display', 'inline-flex' );
		$css->add_property( 'align-items', 'center' );
		$css->add_property( 'justify-content', 'center' );
		$css->add_property( 'line-height', '1em' );
		$css->add_property( 'text-decoration', 'none !important' );
		$css->add_property( 'transition', '.2s background-color ease-in-out, .2s color ease-in-out, .2s border-color ease-in-out, .2s opacity ease-in-out, .2s box-shadow ease-in-out' );

		foreach ( $data as $atts ) {
			if ( ! isset( $atts['uniqueId'] ) ) {
				continue;
			}

			$defaults = flexblocks_get_block_defaults();

			$settings = wp_parse_args(
				$atts,
				$defaults['button']
			);

			$id = $atts['uniqueId'];

			$css->set_selector( 'a.fx-button-' . $id );
			$css->add_property( 'background-color', $settings['backgroundColor'] );
			$css->add_property( 'color', $settings['textColor'] );
			$css->add_property( 'font-size', $settings['fontSize'], 'em' );
			$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTop'], $settings['paddingRight'], $settings['paddingBottom'], $settings['paddingLeft'], 'px' ) );
			$css->add_property( 'border-radius', flexblocks_get_shorthand_css( $settings['borderRadiusTopLeft'], $settings['borderRadiusTopRight'], $settings['borderRadiusBottomRight'], $settings['borderRadiusBottomLeft'], 'px' ) );
			$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTop'], $settings['marginRight'], $settings['marginBottom'], $settings['marginLeft'], 'px' ) );
			$css->add_property( 'border-width', flexblocks_get_shorthand_css( $settings['borderSizeTop'], $settings['borderSizeRight'], $settings['borderSizeBottom'], $settings['borderSizeLeft'], 'px' ) );

			if ( $settings['borderSizeTop'] || $settings['borderSizeRight'] || $settings['borderSizeBottom'] || $settings['borderSizeLeft'] ) {
				$css->add_property( 'border-style', 'solid' );
			}

			$css->add_property( 'border-color', $settings['borderColor'] );
			$css->add_property( 'text-transform', $settings['textTransform'] );

			$css->set_selector( 'a.fx-button-' . $id . ':hover,a.fx-button-' . $id . ':active, a.fx-button-' . $id . ':focus' );
			$css->add_property( 'background-color', $settings['backgroundColorHover'] );
			$css->add_property( 'color', $settings['textColorHover'] );
			$css->add_property( 'border-color', $settings['borderColorHover'] );

			$css->start_media_query( apply_filters( 'flexblocks_tablet_media_query', '(max-width: 1024px)' ) );
				$css->set_selector( 'a.fx-button-' . $id );
				$css->add_property( 'font-size', $settings['fontSizeTablet'], 'em' );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopTablet'], $settings['paddingRightTablet'], $settings['paddingBottomTablet'], $settings['paddingLeftTablet'], 'px' ) );
				$css->add_property( 'border-radius', flexblocks_get_shorthand_css( $settings['borderRadiusTopRightTablet'], $settings['borderRadiusBottomRightTablet'], $settings['borderRadiusBottomLeftTablet'], $settings['borderRadiusTopLeftTablet'], 'px' ) );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopTablet'], $settings['marginRightTablet'], $settings['marginBottomTablet'], $settings['marginLeftTablet'], 'px' ) );
				$css->add_property( 'border-width', flexblocks_get_shorthand_css( $settings['borderSizeTopTablet'], $settings['borderSizeRightTablet'], $settings['borderSizeBottomTablet'], $settings['borderSizeLeftTablet'], 'px' ) );
			$css->stop_media_query();

			$css->start_media_query( apply_filters( 'flexblocks_mobile_media_query', '(max-width:768px)' ) );
				$css->set_selector( 'a.fx-button-' . $id );
				$css->add_property( 'font-size', $settings['fontSizeMobile'], 'em' );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopMobile'], $settings['paddingRightMobile'], $settings['paddingBottomMobile'], $settings['paddingLeftMobile'], 'px' ) );
				$css->add_property( 'border-radius', flexblocks_get_shorthand_css( $settings['borderRadiusTopRightMobile'], $settings['borderRadiusBottomRightMobile'], $settings['borderRadiusBottomLeftMobile'], $settings['borderRadiusTopLeftMobile'], 'px' ) );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopMobile'], $settings['marginRightMobile'], $settings['marginBottomMobile'], $settings['marginLeftMobile'], 'px' ) );
				$css->add_property( 'border-width', flexblocks_get_shorthand_css( $settings['borderSizeTopMobile'], $settings['borderSizeRightMobile'], $settings['borderSizeBottomMobile'], $settings['borderSizeLeftMobile'], 'px' ) );
			$css->stop_media_query();
		}

		return $css->css_output();
	}

	/**
	 * Get our Headline block CSS.
	 *
	 * @since 0.1
	 */
	if ( 'headline' === $block ) {
		$data = flexblocks_get_block_data( 'flexblocks/headline', $content );

		if ( empty( $data ) ) {
			return;
		}

		$css = new FlexBlocks_Dynamic_CSS;

		foreach ( $data as $atts ) {
			if ( ! isset( $atts['uniqueId'] ) ) {
				continue;
			}

			$defaults = flexblocks_get_block_defaults();

			$settings = wp_parse_args(
				$atts,
				$defaults['headline']
			);

			$id = $atts['uniqueId'];

			$css->set_selector( '.fx-headline-' . $id );
			$css->add_property( 'font-family', $settings['fontFamily'] );
			$css->add_property( 'text-align', $settings['alignment'] );
			$css->add_property( 'background-color', $settings['backgroundColor'] );
			$css->add_property( 'color', $settings['textColor'] );
			$css->add_property( 'font-size', $settings['fontSize'], 'px' );
			$css->add_property( 'font-weight', $settings['fontWeight'] );
			$css->add_property( 'text-transform', $settings['textTransform'] );
			$css->add_property( 'line-height', $settings['lineHeight'], 'em' );
			$css->add_property( 'letter-spacing', $settings['letterSpacing'], 'em' );
			$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTop'], $settings['marginRight'], $settings['marginBottom'], $settings['marginLeft'], 'px' ) );
			$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTop'], $settings['paddingRight'], $settings['paddingBottom'], $settings['paddingLeft'], 'px' ) );

			$css->set_selector( '.fx-headline-' . $id . ' a, .fx-headline-' . $id . ' a:visited' );
			$css->add_property( 'color', $settings['linkColor'] );

			$css->set_selector( '.fx-headline-' . $id . ' a:hover' );
			$css->add_property( 'color', $settings['linkColorHover'] );

			$css->start_media_query( apply_filters( 'flexblocks_tablet_media_query', '(max-width: 1024px)' ) );
				$css->set_selector( '.fx-headline-' . $id );
				$css->add_property( 'text-align', $settings['alignmentTablet'] );
				$css->add_property( 'font-size', $settings['fontSizeTablet'], 'px' );
				$css->add_property( 'line-height', $settings['lineHeightTablet'], 'em' );
				$css->add_property( 'letter-spacing', $settings['letterSpacingTablet'], 'em' );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopTablet'], $settings['marginRightTablet'], $settings['marginBottomTablet'], $settings['marginLeftTablet'], 'px' ) );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopTablet'], $settings['paddingRightTablet'], $settings['paddingBottomTablet'], $settings['paddingLeftTablet'], 'px' ) );
			$css->stop_media_query();


			$css->start_media_query( apply_filters( 'flexblocks_mobile_media_query', '(max-width:768px)' ) );
				$css->set_selector( '.fx-headline-' . $id );
				$css->add_property( 'text-align', $settings['alignmentMobile'] );
				$css->add_property( 'font-size', $settings['fontSizeMobile'], 'px' );
				$css->add_property( 'line-height', $settings['lineHeightMobile'], 'em' );
				$css->add_property( 'letter-spacing', $settings['letterSpacingMobile'], 'em' );
				$css->add_property( 'margin', flexblocks_get_shorthand_css( $settings['marginTopMobile'], $settings['marginRightMobile'], $settings['marginBottomMobile'], $settings['marginLeftMobile'], 'px' ) );
				$css->add_property( 'padding', flexblocks_get_shorthand_css( $settings['paddingTopMobile'], $settings['paddingRightMobile'], $settings['paddingBottomMobile'], $settings['paddingLeftMobile'], 'px' ) );
			$css->stop_media_query();
		}

		return $css->css_output();
	}
}

add_action( 'wp_head', 'flexblocks_do_frontend_block_css', 200 );
/**
 * Print our CSS for each block.
 *
 * @since 0.1
 */
function flexblocks_do_frontend_block_css() {

	$container_css = flexblocks_get_dynamic_css( 'container' );
	$button_container_css = flexblocks_get_dynamic_css( 'button-container' );
	$button_css = flexblocks_get_dynamic_css( 'button' );
	$headline_css = flexblocks_get_dynamic_css( 'headline' );
	$grid_container_css = flexblocks_get_dynamic_css( 'grid' );

	echo '<style>';
		echo $container_css . $button_container_css . $button_css . $headline_css . $grid_container_css;
	echo '</style>';
}
