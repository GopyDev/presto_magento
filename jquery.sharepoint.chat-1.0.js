(function($) {
	$.chat=function(options) {
		$.chat.settings = $.extend({
		listguid:'0',
		divid:'jQueryChatBox',
		savecaption:'Send',
		clearcaption:'Clear',
		messagecolumn:'Message'
		}, options || {});
		ChatBox="<input type='text'  size='50' id='" + $.chat.settings.divid + "textbox' name='" + $.chat.settings.divid + "textbox'/><br/><input type='submit' id='"+ $.chat.settings.divid +"submitbutton' value='" + $.chat.settings.savecaption + "'/><input type='reset' value='" + $.chat.settings.clearcaption + "'/>";
		$('#' + $.chat.settings.divid).html(ChatBox);
		$('#' + $.chat.settings.divid +'submitbutton').click(function() {
			if ($.trim($('#'+$.chat.settings.divid + 'textbox').val())=="") return false;
    		var soapEnv = "<?xml version=\"1.0\" encoding=\"utf-8\"?> \
		        <soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" \
	            xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" \
	            xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\"> \
	          <soap:Body> \
	            <UpdateListItems xmlns=\"http://schemas.microsoft.com/sharepoint/soap/\"> \
	              <listName>"+$.chat.settings.listguid+"</listName> \
	            	  <updates> \
	            	    <Batch OnError=\"Continue\"> \
	            			<Method ID=\"1\" Cmd=\"New\"> \
	            	    		<Field Name=\""+$.chat.settings.messagecolumn+"\">" + $('#'+$.chat.settings.divid + 'textbox').val() + "</Field> \
	           				 </Method> \
	        			</Batch> \
	        		</updates> \
	            </UpdateListItems> \
	          </soap:Body> \
	        </soap:Envelope>";
		    $.ajax({
		        url: L_Menu_BaseUrl + "/_vti_bin/lists.asmx",
		        beforeSend: function(xhr) {
		            xhr.setRequestHeader("SOAPAction",
		            "http://schemas.microsoft.com/sharepoint/soap/UpdateListItems");
		        },
		        type: "POST",
		        dataType: "xml",
		        data: soapEnv,
		        contentType: "text/xml; charset=utf-8"
		    });
			$('#'+$.chat.settings.divid + 'textbox').val('');		    
			return false;
		});
	}
})(jQuery);

$(document).ready(function() {
		$.livelistdata(
			{
				prompt:false,
				interval:100,
				opacity:'0.5'
			}
		);
});