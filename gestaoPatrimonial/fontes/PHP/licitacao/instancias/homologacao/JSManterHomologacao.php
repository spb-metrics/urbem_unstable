<script language="JavaScript">
    
    function limpaCampos () {
        jQuery('#inCodModalidade').val('');
        jQuery('#inCodLicitacao').val('');
        jQuery('#stDtHomologacao').val('');
        jQuery('#stHoraHomologacao').val('');
        jQuery('#stProcesso').html('&nbsp;');
        jQuery('#inCodModalidade').attr('disabled',true);
        jQuery('#inCodLicitacao').find('option').remove().end().append('<option value=\'\'>Selecione</option>').val('');
    }
    
</script>
