/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
  config.height = '600px';
  config.toolbarGroups = [
    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'links' },
    { name: 'insert' },
    { name: 'tools' },
//    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
    { name: 'others' },
    '/',
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
    { name: 'styles' },
    { name: 'colors' },
    { name: 'about' }
  ];
};
