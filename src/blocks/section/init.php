<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'enqueue_block_editor_assets', 'generate_enqueue_section_block_scripts' );
/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function generate_enqueue_section_block_scripts() {
	wp_enqueue_script(
		'generatepress-blocks',
		GENERATE_BLOCK_MODULE_DIR_URL . 'dist/blocks.build.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
		filemtime( GENERATE_BLOCK_MODULE_DIR . 'dist/blocks.build.js' ),
		true
	);

	wp_enqueue_style(
		'generatepress-blocks',
		GENERATE_BLOCK_MODULE_DIR_URL . 'dist/blocks.editor.build.css',
		array( 'wp-edit-blocks' ),
		filemtime( GENERATE_BLOCK_MODULE_DIR . 'dist/blocks.editor.build.css' )
	);

	if ( function_exists( 'generate_get_option' ) ) {
		$css = 'body.wp-admin .editor-styles-wrapper .grid-container {max-width: ' . generate_get_option( 'container_width' ) . 'px;margin-left: auto;margin-right:auto;';
		wp_add_inline_style( 'generatepress-section-block', $css );
	}
}

function generate_get_nested_section_block_data( $block, $data ) {
	if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $inner_block ) {
			if ( 'generatepress/section' === $inner_block['blockName'] ) {
				$data[] = $inner_block['attrs'];
			}

			$data = generate_get_nested_section_block_data( $inner_block, $data );
		}
	}

	return $data;
}

function generate_get_section_block_data() {
	if ( ! function_exists( 'has_blocks' ) ) {
		return;
	}

	if ( is_singular() && has_blocks( get_the_ID() ) ) {
		global $post;

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( ! function_exists( 'parse_blocks' ) ) {
			return;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( ! is_array( $blocks ) || empty( $blocks ) ) {
			return;
		}

		$data = array();

		foreach ( $blocks as $index => $block ) {
			if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {
				if ( 'generatepress/section' === $block['blockName'] ) {
					$data[] = $block['attrs'];

					$data = generate_get_nested_section_block_data( $block, $data );
				}

				if ( 'core/block' === $block['blockName'] ) {
					$atts = $block['attrs'];

					if ( isset( $atts['ref'] ) ) {
						$reusable_block = get_post( $atts['ref'] );

						if ( $reusable_block && 'wp_block' === $reusable_block->post_type ) {
							$blocks = parse_blocks( $reusable_block->post_content );

							foreach ( $blocks as $index => $block ) {
								if ( 'generatepress/section' === $block['blockName'] ) {
									$data[] = $block['attrs'];

									$data = generate_get_nested_section_block_data( $block, $data );
								}
							}
						}
					}
				}
			}
		}

		return $data;
	}
}

add_action( 'wp_enqueue_scripts', 'generate_do_section_block_frontend_css', 200 );
/**
 * Print our CSS for each section.
 *
 * @since 1.8
 */
function generate_do_section_block_frontend_css() {

	$data = generate_get_section_block_data();

	if ( empty( $data ) ) {
		return;
	}

	$css = '';

	foreach ( $data as $atts ) {
		if ( ! isset( $atts['uniqueId'] ) ) {
			continue;
		}

		$id = 'section-' . $atts['uniqueId'];

		$values = array(
			'outer_container' => isset( $atts['outerContainer'] ) ? $atts['outerContainer'] : 'full',
			'inner_container' => isset( $atts['innerContainer'] ) ? $atts['innerContainer'] : 'contained',
			'background_color' => isset( $atts['backgroundColor'] ) ? 'background-color:' . $atts['backgroundColor'] . ';' : '',
			'text_color' => isset( $atts['textColor'] ) ? 'color:' . $atts['textColor'] . ';' : '',
			'padding_top' => isset( $atts['paddingTop'] ) ? 'padding-top:' . $atts['paddingTop'] . 'px;' : 'padding-top: 10px;',
			'padding_right' => isset( $atts['paddingRight'] ) ? 'padding-right:' . $atts['paddingRight'] . 'px;' : 'padding-right: 10px;',
			'padding_bottom' => isset( $atts['paddingBottom'] ) ? 'padding-bottom:' . $atts['paddingBottom'] . 'px;' : 'padding-bottom: 10px;',
			'padding_left' => isset( $atts['paddingLeft'] ) ? 'padding-left:' . $atts['paddingLeft'] . 'px;' : 'padding-left: 10px;',
			'padding_top_mobile' => isset( $atts['paddingTopMobile'] ) ? 'padding-top:' . $atts['paddingTopMobile'] . 'px;' : 'padding-top: 10px;',
			'padding_right_mobile' => isset( $atts['paddingRightMobile'] ) ? 'padding-right:' . $atts['paddingRightMobile'] . 'px;' : 'padding-right: 10px;',
			'padding_bottom_mobile' => isset( $atts['paddingBottomMobile'] ) ? 'padding-bottom:' . $atts['paddingBottomMobile'] . 'px;' : 'padding-bottom: 10px;',
			'padding_left_mobile' => isset( $atts['paddingLeftMobile'] ) ? 'padding-left:' . $atts['paddingLeftMobile'] . 'px;' : 'padding-left: 10px;',
			'column_gutter' => isset( $atts['columnGutter'] ) ? $atts['columnGutter'] : '',
			'column_gutter_mobile' => isset( $atts['columnGutterMobile'] ) ? $atts['columnGutterMobile'] : '',
			'link_color' => isset( $atts['linkColor'] ) ? 'color:' . $atts['linkColor'] . ';' : '',
			'link_color_hover' => isset( $atts['linkColorHover'] ) ? 'color:' . $atts['linkColorHover'] . ';' : '',
			'background_image' => isset( $atts['bgImage'] ) ? $atts['bgImage'] : '',
			'background_options' => isset( $atts['bgOptions'] ) ? $atts['bgOptions'] : '',
		);

		$container_width = 1100;

		if ( function_exists( 'generate_get_option' ) ) {
			$container_width = generate_get_option( 'container_width' );
		}

		if ( 'contained' === $values['outer_container'] ) {
			$css .= '.generate-section.' . $id . '{max-width: ' . $container_width . 'px;margin-left: auto;margin-right: auto;}';
		}

		if ( 'contained' === $values['inner_container'] ) {
			$css .= '.generate-section.' . $id . ' .inside-section{max-width: ' . $container_width . 'px;margin-left: auto;margin-right: auto;}';
		}

		if ( $values['background_color'] || $values['text_color'] ) {
			$css .= '.generate-section.' . $id . '{' . $values['background_color'] . $values['text_color'] . '}';
		}

		if ( $values['background_image'] ) {
			$url = $values['background_image']['image']['url'];

			$background_position = 'center center';
			$background_size = 'cover';
			$background_repeat = 'no-repeat';
			$background_attachment = '';

			if ( ! empty( $values['background_options']['position'] ) ) {
				$background_position = $values['background_options']['position'];
			}

			if ( ! empty( $values['background_options']['size'] ) ) {
				$background_size = $values['background_options']['size'];
			}

			if ( ! empty( $values['background_options']['repeat'] ) ) {
				$background_repeat = $values['background_options']['repeat'];
			}

			if ( ! empty( $values['background_options']['attachment'] ) ) {
				$background_attachment = 'background-attachment: ' . $values['background_options']['attachment'] . ';';
			}

			if ( $values['background_color'] && isset( $values['background_options']['overlay'] ) && $values['background_options']['overlay'] ) {
				$css .= '.generate-section.' . $id . '{background-image: linear-gradient(0deg, ' . $atts['backgroundColor'] . ', ' . $atts['backgroundColor'] . '), url(' . $url . ');background-size: ' . $background_size . ';background-position: ' . $background_position . ';background-repeat: ' . $background_repeat . ';' . $background_attachment . '}';
			} else {
				$css .= '.generate-section.' . $id . '{background-image: url(' . $url . ');background-size: ' . $background_size . ';background-position: ' . $background_position . ';background-repeat: ' . $background_repeat . ';' . $background_attachment . '}';
			}
		}

		if ( $values['padding_top'] || $values['padding_right'] || $values['padding_bottom'] || $values['padding_left'] ) {
			$css .= ".generate-section." . $id . " > .inside-section{" . $values['padding_top'] . $values['padding_right'] . $values['padding_bottom'] . $values['padding_left'] . "}";
		}

		if ( $values['link_color'] ) {
			$css .= ".generate-section." . $id . " a, .generate-section." . $id . " a:visited{" . $values['link_color'] . "}";
		}

		if ( $values['link_color_hover'] ) {
			$css .= ".generate-section." . $id . " a:hover{" . $values['link_color_hover'] . "}";
		}

		if ( $values['column_gutter'] || 0 === $values['column_gutter'] ) {
			$css .= ".generate-section." . $id . " .wp-block-columns {margin-left: -" . $values['column_gutter'] . "px}";
			$css .= ".generate-section." . $id . " .wp-block-columns .wp-block-column {margin-left: " . $values['column_gutter'] . "px}";
		}

		if (
			$values['padding_top_mobile'] ||
			$values['padding_right_mobile'] ||
			$values['padding_bottom_mobile'] ||
			$values['padding_left_mobile'] ||
			$values['column_gutter_mobile']
		) {
			$media_query = apply_filters( 'generate_mobile_media_query', '(max-width:768px)' );
			$css .= "@media " . $media_query . " {";
				if (
					$values['padding_top_mobile'] ||
					$values['padding_right_mobile'] ||
					$values['padding_bottom_mobile'] ||
					$values['padding_left_mobile']
				) {
					$css .= ".generate-section." . $id . " > .inside-section{" . $values['padding_top_mobile'] . $values['padding_right_mobile'] . $values['padding_bottom_mobile'] . $values['padding_left_mobile'] . "}";
				}

				if ( $values['column_gutter_mobile'] || 0 === $values['column_gutter_mobile'] ) {
					$css .= ".generate-section." . $id . " .wp-block-columns .wp-block-column {margin-bottom: " . $values['column_gutter_mobile'] . "px}";
				}
			$css .= "}";
		}
	}

	$css .= '.inside-section > *:last-child {margin-bottom:0}';

	wp_add_inline_style( 'generate-style', $css );
}
