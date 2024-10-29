// JavaScript Document

(function(){
  tinymce.create('tinymce.plugins.ADAL_lite', {
    init: function(ed, url){
      ed.addButton('ADAL_myblockquotebtn_lite', {
        title: 'ADAL Productos Lite',
        cmd: 'ADAL_myBlockquoteBtnCmd_lite',
        image: url + '/img/adaico_lite.png'
      });
      ed.addCommand('ADAL_myBlockquoteBtnCmd_lite', function(){
        var selectedText = ed.selection.getContent({format: 'html'});	
		 var estado;
        var win = ed.windowManager.open({
          title: 'ADAL Productos Lite',
		  body: [
            {
              type: 'textbox',
              name: 'articulos',
              label: 'Articulos',
              minWidth: 500,
              value: '',
			tooltip: 'Ingresa los codigos de Amazon separados por coma, máximo 3',
				
            },
			{
              type: 'textbox',
              name: 'texto_boton',
              label: 'Texto boton',
              minWidth: 500,
              value: 'Ver Producto',
			tooltip: 'Texto del boton de compra'
            }
																					
          ],
          buttons: [
            {
              text: "Ok",
              subtype: "primary",
              onclick: function() {
                win.submit();
              }
            },
            {
              text: "Cancel",
              onclick: function() {
                win.close();
              }
            }
          ],
          onsubmit: function(e){
            
			 var art, plant, tboton, cantv, asy; 
            if( e.data.articulos.length > 0 ) {
				art=e.data.articulos;
              //params.push('author="' + e.data.author + '"');
            }
			  else
				  {
					  return false;
				  }
            
			  if( e.data.texto_boton.length > 0 ) {
              tboton=e.data.texto_boton;
            }
		
			var returnText = '[amazon_lite articulos="'+art+'" tboton="'+tboton+'"]';			  
            
            ed.execCommand('mceInsertContent', 0, returnText);
           }
        });
      });
    },
    getInfo: function() {
      return {
        longname : 'ADAL Wordpress Plugin',
        author : 'Linkkos Mexico',
        authorurl : 'https://www.linkkos.com',
        version : "1.0"
      };
    }
  });
  tinymce.PluginManager.add( 'ADAL_mytinymceplugin_lite', tinymce.plugins.ADAL_lite );
})();