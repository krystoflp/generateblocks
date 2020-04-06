/**
 * Block: Button Container
 */

import classnames from 'classnames';
import DimensionsControl from '../../components/dimensions/';
import getIcon from '../../utils/get-icon';

const { __ } = wp.i18n; // Import __() from wp.i18n
const {
	TextControl,
	PanelBody,
	RangeControl,
	Notice,
	Tooltip,
	Button,
	IconButton,
	BaseControl,
	ToggleControl,
	Toolbar,
} = wp.components;

const {
	Fragment,
	Component
} = wp.element;

const {
	InspectorControls,
	InspectorAdvancedControls,
	InnerBlocks,
	AlignmentToolbar,
	BlockControls,
} = wp.blockEditor;

const {
	createBlock,
	cloneBlock,
} = wp.blocks;

const ELEMENT_ID_REGEX = /[\s#]/g;
const gbButtonContainerIds = [];

const ALIGNMENT_CONTROLS = [
	{
		icon: 'editor-alignleft',
		title: __( 'Align Buttons Left', 'generateblocks' ),
		align: 'left',
	},
	{
		icon: 'editor-aligncenter',
		title: __( 'Align Buttons Center', 'generateblocks' ),
		align: 'center',
	},
	{
		icon: 'editor-alignright',
		title: __( 'Align Buttons Right', 'generateblocks' ),
		align: 'right',
	},
];

class GenerateButtonContainer extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
            selectedDevice: 'desktop',
        };
	}

	componentDidMount() {
		let id = this.props.clientId.substr( 2, 9 ).replace( '-', '' );

		if ( ! this.props.attributes.uniqueId ) {
			this.props.setAttributes( {
				uniqueId: id,
			} );

			gbButtonContainerIds.push( id );
		} else if ( gbButtonContainerIds.includes( this.props.attributes.uniqueId ) ) {

			this.props.setAttributes( {
				uniqueId: id,
			} );

			gbButtonContainerIds.push( id );
		} else {
			gbButtonContainerIds.push( this.props.attributes.uniqueId );
		}

		const thisBlock = wp.data.select( 'core/block-editor' ).getBlocksByClientId( this.props.clientId )[ 0 ];

		if ( thisBlock ) {
			const childBlocks = thisBlock.innerBlocks;

			if ( 0 === childBlocks.length ) {
				wp.data.dispatch( 'core/block-editor' ).insertBlocks( wp.blocks.createBlock( 'generateblocks/button', generateBlocksStyling.button ), undefined, this.props.clientId );
			}
		}
	}

	render() {
		const {
			attributes,
			setAttributes,
			clientId,
		} = this.props;

		const {
            selectedDevice,
        } = this.state;

		const {
			uniqueId,
			elementId,
			cssClasses,
			alignment,
			alignmentTablet,
			alignmentMobile,
			marginTop,
			marginRight,
			marginBottom,
			marginLeft,
			marginUnit,
			marginSyncUnits,
			marginTopTablet,
			marginRightTablet,
			marginBottomTablet,
			marginLeftTablet,
			marginTopMobile,
			marginRightMobile,
			marginBottomMobile,
			marginLeftMobile,
			stack,
			stackTablet,
			stackMobile,
			fillHorizontalSpace,
			fillHorizontalSpaceTablet,
			fillHorizontalSpaceMobile,
		} = attributes;

		let flexAlignment = '',
			stacked = '',
			fillHorizontal = '',
			stackAndFill = '';

		if ( 'right' === alignment ) {
			flexAlignment = 'flex-end';
		} else if ( 'left' === alignment ) {
			flexAlignment = 'flex-start';
		} else {
			flexAlignment = alignment;
		}

		if ( stack ) {
			stacked = `
				.gb-button-wrapper-` + uniqueId + ` {
					flex-direction: column;
					align-items: ` + flexAlignment + `;
				}

				.gb-button-wrapper-` + uniqueId + ` > .block-editor-inner-blocks > .block-editor-block-list__layout {
					flex-direction: column;
				}
			`
		}

		if ( fillHorizontalSpace ) {
			fillHorizontal = `
				.gb-button-wrapper-` + uniqueId + ` {
					display: block;
				}

				.gb-button-wrapper-` + uniqueId + ` > .block-editor-inner-blocks > .block-editor-block-list__layout > .wp-block {
					flex: 1;
				}

				.gb-button-wrapper-` + uniqueId + ` > .components-button {
					background: #fff;
					border: 1px solid #ddd;
					margin-top: 10px;
				}
			`
		}

		if ( stack && fillHorizontalSpace ) {
			stackAndFill = `
				.gb-button-wrapper-` + uniqueId + ` > .block-editor-inner-blocks > .block-editor-block-list__layout > .wp-block {
					width: 100% !important;
					box-sizing: border-box;
				}
			`
		}

		const css = `
			.gb-button-wrapper-` + uniqueId + ` {
			  margin-top: ` + marginTop + marginUnit + `;
			  margin-right: ` + marginRight + marginUnit + `;
			  margin-bottom: ` + marginBottom + marginUnit + `;
			  margin-left: ` + marginLeft + marginUnit + `;
			  justify-content: ` + flexAlignment + `;
			}

			` + stacked + fillHorizontal + stackAndFill + `
		`

		return (
			<Fragment>
				<BlockControls>
					<Toolbar>
						<Tooltip text={ __( 'Add Button', 'generateblocks' ) }>
							<IconButton
								className="gblocks-add-new-button"
								icon={ 'insert' }
								onClick={ () => {
									const thisBlock = wp.data.select( 'core/block-editor' ).getBlocksByClientId( clientId )[ 0 ];

									if ( thisBlock ) {
										const childBlocks = thisBlock.innerBlocks;
										const keys = Object.keys( childBlocks );
										const lastKey = keys[ keys.length - 1 ];

										if ( typeof childBlocks[ lastKey ] !== 'undefined' ) {
											const blockToCopyId = childBlocks[ lastKey ].clientId;

											if ( blockToCopyId ) {
												const blockToCopy = wp.data.select( 'core/block-editor' ).getBlocksByClientId( blockToCopyId )[ 0 ];
												const clonedBlock = cloneBlock( blockToCopy );

												wp.data.dispatch( 'core/block-editor' ).insertBlocks( clonedBlock, undefined, clientId );
											}
										} else if ( 0 === childBlocks.length ) {
											wp.data.dispatch( 'core/block-editor' ).insertBlocks( wp.blocks.createBlock( 'generateblocks/button', generateBlocksStyling.button ), undefined, clientId );
										}
									}
								} }
							/>
						</Tooltip>
					</Toolbar>
					<AlignmentToolbar
						isCollapsed={ false }
						value={ alignment }
						alignmentControls={ ALIGNMENT_CONTROLS }
						onChange={ ( nextAlign ) => {
							setAttributes( { alignment: nextAlign } );
						} }
					/>
				</BlockControls>

				<InspectorControls>
					<div className="gb-responsive-tabs">
						<Tooltip text={ __( 'Show options for all devices', 'generateblocks' ) } key={ 'desktop-tab' }>
							<Button
								isLarge
								isPressed={ 'desktop' === selectedDevice ? true : false }
								onClick={ () => {
									this.setState( {
										selectedDevice: 'desktop',
									} );
								} }
							>
								{ __( 'Desktop', 'generateblocks' ) }
							</Button>
						</Tooltip>

						<Tooltip text={ __( 'Show options for tablet devices' ) } key={ 'tablet-tab' }>
							<Button
								isLarge
								isPressed={ 'tablet' === selectedDevice ? true : false }
								onClick={ () => {
									this.setState( {
										selectedDevice: 'tablet',
									} );
								} }
							>
								{ __( 'Tablet', 'generateblocks' ) }
							</Button>
						</Tooltip>

						<Tooltip text={ __( 'Show options for mobile devices' ) } key={ 'desktop-tab' }>
							<Button
								isLarge
								isPressed={ 'mobile' === selectedDevice ? true : false }
								onClick={ () => {
									this.setState( {
										selectedDevice: 'mobile',
									} );
								} }
							>
								{ __( 'Mobile', 'generateblocks' ) }
							</Button>
						</Tooltip>
					</div>

					<PanelBody
						title={ __( 'Spacing', 'generateblocks' ) }
						initialOpen={ true }
						icon={ getIcon( 'spacing' ) }
						className={ 'gblocks-panel-label' }
					>
						{ 'desktop' === selectedDevice && (
							<Fragment>
								<AlignmentToolbar
									isCollapsed={ false }
									value={ alignment }
									alignmentControls={ ALIGNMENT_CONTROLS }
									onChange={ ( value ) => {
										setAttributes( { alignment: value } );
									} }
								/>

								<DimensionsControl { ...this.props }
									type={ 'margin' }
									label={ __( 'Margin', 'generateblocks' ) }
									attrTop={ 'marginTop' }
									attrRight={ 'marginRight' }
									attrBottom={ 'marginBottom' }
									attrLeft={ 'marginLeft' }
									attrUnit={ 'marginUnit' }
									attrSyncUnits={ 'marginSyncUnits' }
								/>

								<ToggleControl
									label={ __( 'Stack Vertically', 'generateblocks' ) }
									checked={ !! stack }
									onChange={ ( value ) => {
										setAttributes( {
											stack: value
										} );
									} }
								/>

								<ToggleControl
									label={ __( 'Fill Horizontal Space', 'generateblocks' ) }
									checked={ !! fillHorizontalSpace }
									onChange={ ( value ) => {
										setAttributes( {
											fillHorizontalSpace: value
										} );
									} }
								/>
							</Fragment>

						) }

						{ 'tablet' === selectedDevice && (
							<Fragment>
								<AlignmentToolbar
									isCollapsed={ false }
									value={ alignmentTablet }
									onChange={ ( value ) => {
										setAttributes( { alignmentTablet: value } );
									} }
								/>

								<DimensionsControl { ...this.props }
									type={ 'margin' }
									label={ __( 'Margin', 'generateblocks' ) }
									attrTop={ 'marginTopTablet' }
									attrRight={ 'marginRightTablet' }
									attrBottom={ 'marginBottomTablet' }
									attrLeft={ 'marginLeftTablet' }
									attrUnit={ 'marginUnit' }
									attrSyncUnits={ 'marginSyncUnits' }
								/>

								<ToggleControl
									label={ __( 'Stack Vertically', 'generateblocks' ) }
									checked={ !! stackTablet }
									onChange={ ( value ) => {
										setAttributes( {
											stackTablet: value
										} );
									} }
								/>

								<ToggleControl
									label={ __( 'Fill Horizontal Space', 'generateblocks' ) }
									checked={ !! fillHorizontalSpaceTablet }
									onChange={ ( value ) => {
										setAttributes( {
											fillHorizontalSpaceTablet: value
										} );
									} }
								/>
							</Fragment>
						) }

						{ 'mobile' === selectedDevice && (
							<Fragment>
								<AlignmentToolbar
									isCollapsed={ false }
									value={ alignmentMobile }
									onChange={ ( value ) => {
										setAttributes( { alignmentMobile: value } );
									} }
								/>

								<DimensionsControl { ...this.props }
									type={ 'margin' }
									label={ __( 'Margin', 'generateblocks' ) }
									attrTop={ 'marginTopMobile' }
									attrRight={ 'marginRightMobile' }
									attrBottom={ 'marginBottomMobile' }
									attrLeft={ 'marginLeftMobile' }
									attrUnit={ 'marginUnit' }
									attrSyncUnits={ 'marginSyncUnits' }
								/>

								<ToggleControl
									label={ __( 'Stack Vertically', 'generateblocks' ) }
									checked={ !! stackMobile }
									onChange={ ( value ) => {
										setAttributes( {
											stackMobile: value
										} );
									} }
								/>

								<ToggleControl
									label={ __( 'Fill Horizontal Space', 'generateblocks' ) }
									checked={ !! fillHorizontalSpaceMobile }
									onChange={ ( value ) => {
										setAttributes( {
											fillHorizontalSpaceMobile: value
										} );
									} }
								/>
							</Fragment>
						) }
					</PanelBody>

					{ 'desktop' === selectedDevice &&
						<PanelBody
							title={ __( 'Advanced', 'generateblocks' ) }
							initialOpen={ false }
							icon={ getIcon( 'advanced' ) }
							className={ 'gblocks-panel-label' }
						>
							<TextControl
								label={ __( 'Element ID', 'generateblocks' ) }
								value={ elementId }
								onChange={ ( elementId ) => {
									elementId = elementId.replace( ELEMENT_ID_REGEX, '-' );
									setAttributes( { elementId } );
								} }
							/>

							<TextControl
								label={ __( 'CSS Classes', 'generateblocks' ) }
								value={ cssClasses }
								onChange={ ( cssClasses ) => { setAttributes( { cssClasses } ) } }
							/>
						</PanelBody>
					}

					<PanelBody
						title={ __( 'Documentation', 'generateblocks' ) }
						icon={ getIcon( 'documentation' ) }
						initialOpen={ false }
						className={ 'gblocks-panel-label' }
					>
						<p>{ __( 'Need help with this block?', 'generateblocks' ) }</p>
						<a href="https://docs.generateblocks.com/collection/buttons/" target="_blank" rel="noreferrer noopener">{ __( 'Visit our documentation', 'generateblocks' ) }</a>
					</PanelBody>
				</InspectorControls>

				<style>{ css }</style>

				<div
					id={ !! elementId ? elementId : undefined }
					className={ classnames( {
						'gb-button-wrapper': true,
						[`gb-button-wrapper-${ uniqueId }`]: true,
						[`${ cssClasses }`]: '' !== cssClasses
					} ) }
				>
					<InnerBlocks
						allowedBlocks={ [ 'generateblocks/button' ] }
						renderAppender={ () => (
							<Tooltip text={ __( 'Add Button', 'generateblocks' ) }>
								<Button
									className="gblocks-add-new-button gblocks-button-container-appender"
									icon={ 'insert' }
									onClick={ () => {
										wp.data.dispatch( 'core/block-editor' ).insertBlocks( wp.blocks.createBlock( 'generateblocks/button', generateBlocksStyling.button ), undefined, clientId );
									} }
								/>
							</Tooltip>
						) }
					/>
				</div>
			</Fragment>
		);
	}
}

export default ( GenerateButtonContainer );
