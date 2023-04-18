( function( editor, components, i18n, element ) {
	const { __ } = wp.i18n;
	

	var posttypeOptions = [];


	var el = wp.element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.editor.InspectorControls;
	var PanelRow = wp.components.PanelRow;
	var TextareaControl = wp.components.TextareaControl;
	var ServerSideRender = wp.components.ServerSideRender;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var TextControl = wp.components.TextControl;

	var posttypesContent = {};
  
	registerBlockType( 'jci/jcipro-block-script', { 
		title: __( 'Select a JSON-Use-Set', 'json-content-importer-pro'),
		description: __( 'Block with API-data', 'json-content-importer-pro'), 
 		icon: 'welcome-add-page', 
		category: 'widgets', 
    	keywords: ['json', 'jci'],
			template: {
				type: 'string',
				default: 'show all JSON-data: {{ context  json_encode }}',
			},
			jusid: {
				type: 'string',
				default: '',
			},
			showjson: {
				type: 'boolean',
				default: true,
			},
			showdebug: {
				type: 'boolean',
				default: false,
			},
			cachetime: {
				type: 'string',
				default: '0',
			},

		edit: function( props ) {
			var attributes = props.attributes;
			var template = props.attributes.template;
			var jusid = props.attributes.jusid;
			var cachetime = props.attributes.cachetime;
			var showjson = props.attributes.showjson;
			var showdebug = props.attributes.showdebug;
			
			return [
				el( InspectorControls, { key: 'inspector' },
                    el(PanelRow,{},
						el(SelectControl, {
							//label: "Select a JSON-Use-Set:",
							options: posttypeOptions,
							multiple : false,
							style: { fontSize: '14px' },
							onChange: function(value) {
								props.setAttributes( { template: posttypesContent[value] } );
								props.setAttributes( { jusid: value } );
							}
						}),					
					),

					el(PanelRow,{},
						el( TextControl, { 
							type: 'Number',
							value: cachetime,
							style: { fontSize: '14px' },
							label: __( 'Insert Integer for Cachetime in Minutes (0 for no Cacheing)', 'json-content-importer-pro' ),
							onChange: function( value ) {
								props.setAttributes( { cachetime: value } );
							},
						} ), 
					),
					
					el(PanelRow,{},
						el( ToggleControl, { 
							type: 'string',
							label: __( 'Show JSON', 'json-content-importer-pro' ),
							checked : !!showjson,
							onChange: function( newtoggleswitch ) {
								props.setAttributes( { showjson: newtoggleswitch } );
							},
						} ), 
					),
					
					el(PanelRow,{},
						el( ToggleControl, { 
							type: 'string',
							label: __( 'Show Debuginfo', 'json-content-importer-pro' ),
							checked : !!showdebug,
							onChange: function( newshowdebug ) {
								props.setAttributes( { showdebug: newshowdebug } );
							},
						} ), 
					),
		
                    el(PanelRow,{},
						el( TextareaControl, {
							type: 'string',
							label: __( 'Template to use for JSON:', 'json-content-importer-pro' ),
							placeholder:  __( 'if emtpy: Version: {{ _context | json_encode}}', 'json-content-importer-pro' ),		
							help : __( 'Use twig-code for the JSON', 'json-content-importer-pro' ),
							rows: 20,
							value: template,
							onChange: function( newTemplate ) {
								props.setAttributes( { template: newTemplate } );
							},
						} ),
						
					),	
						
				),
				el(ServerSideRender, {
					block: 'jci/jcipro-block-script',
					attributes:  props.attributes
				})
			];
		},
		save: function() {
			return null;
		},
	} );
	
	wp.apiFetch({
		path: '/wp/jci/v1/get/option/jci_pro_api_use_items',
		}).then(function(posttypes) {
		//console.log(posttypes);
		const jsonObj = JSON.parse(posttypes);
				posttypeOptions.push({
					value: "",
					label: "Select a JSON-Use-Set"
				});
		for (var key in jsonObj) {
			if (jsonObj[key]["status"]!="inactive") {
				posttypesContent[key] = jsonObj[key]["content"];
			
				//console.log(jsonObj[key]["status"]); 
			
				posttypeOptions.push({
					value: key,
					label: jsonObj[key]["savejusname"]
				});
			}
		}
	});
} )(
	window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
);
