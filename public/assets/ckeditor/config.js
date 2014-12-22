/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  config.toolbarGroups = [
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup', 'colors' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'tools' },
    { name: 'clipboard',   groups: ['mode', 'clipboard', 'undo' ] },
    { name: 'styles' },
    { name: 'links'},
    { name: 'insert' },
//    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
//    { name: 'others' },
//    '/',

//    { name: 'colors' },
//    { name: 'about' }
  ];
  // Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
  config.height = '600px';
  config.allowedContent = true;
};
