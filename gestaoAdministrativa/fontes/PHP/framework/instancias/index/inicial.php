<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FW_LEGADO."dataBaseLegado.class.php"    );

if (!Sessao::started()) {
   header("location:login.php?".Sessao::getId()."&erro=2");
}
//Array de status
//$sSQL = "select parametro,valor from administracao.configuracao WHERE parametro = 'mensagem' and exercicio = '".Sessao::getExercicio()."'";
//$dbEmp = new dataBaseLegado;
//$dbEmp->abreBD();
//$dbEmp->abreSelecao($sSQL);
//$dbEmp->vaiPrimeiro();
//$janela="";
//while (!$dbEmp->eof()) {
//   $divulga  = strip_tags(trim($dbEmp->pegaCampo("valor")));
//   $dbEmp->vaiProximo();
//   $janela .= "$divulga";
//}
//$dbEmp->limpaSelecao();
//$dbEmp->fechaBD();
?>
<script laguage='Javascript'>
    parent.frames["telaStatus"].location.replace('status.php');

</script>
<table width="100%" style="background-color: #EDF4FA">
    <tr>
        <td><img src="<?=CAM_FW_IMAGENS;?>loading_modal.gif" style="display: none"/></td>
        <td><img src="<?=CAM_FW_IMAGENS;?>logo_urbem_grande.png" border=0></td>
        <td>&nbsp;&nbsp;</td>
        <td>
        <font color="#000000" face="Futura, Arial, Helvetica" size=2><b>
        Pacotes Urbem Instalados:<br>
        <?php
        if( defined('VERSAO_GA') )
          echo "- Gestão Administrativa: ".VERSAO_GA."<br>\n";
        if( defined('VERSAO_GF') )
          echo "- Gestão Financeira: ".VERSAO_GF."<br>\n";
        if( defined('VERSAO_GP') )
          echo "- Gestão Patrimonial: ".VERSAO_GP."<br>\n";
        if( defined('VERSAO_GRH') )
          echo "- Gestão Recursos Humanos: ".VERSAO_GRH."<br>\n";
        if( defined('VERSAO_GT') )
          echo "- Gestão Tributária: ".VERSAO_GT."<br>\n";
        if( defined('VERSAO_GPC') )
          echo "- Gestão Prestação de Contas: ".VERSAO_GPC."<br>\n";
        echo "Recomenda-se que o cache do navegador seja limpo.<br>";
        ?>
        <?//=$janela;?>
        </b></font>
        </td>
    </tr>
</table>
<?php
if (isset($_REQUEST['reservaSaldo'])) {
?>
<style type="text/css">
   div.ui-dialog {
      z-index : 9 !important;
      background: #edf4fa;
      padding: 2px;
      border-radius : 2px;
   }

   div#dialog{
      padding: 8px;
      font-weight: bold;
   }

   div.ui-dialog-titlebar {
      margin: 2px;
      padding: 5px;
      background: #4a6491;
      color: #FFFFFF;
      font-weight: bold;
      border-radius : 2px;
   }

   button.ui-dialog-titlebar-close {
      border: 0;
      color: #4a6491;
      background: #d0e4f2;
      margin-left: 20px;
      font-weight: bold;
      border-radius : 2px;
   }

   div.ui-dialog-buttonset {
      text-align: center;
   }

   button.ui-button-text-only {
      border: 0;
      color: orange;
      background: #4a6491;
      font-weight: bold;
      border-radius : 2px;
   }

</style>
<div id="dialog" title="Data da Reserva de Saldo">
   <label>Data&nbsp;</label>
   <input type="text" id="stDataReserva" style="width:90px" />
</div>
<script>
   jq("#stDataReserva").val('<?=date("d/m/Y")?>');

   function desbloqueiaFrames()
   {
      if (typeof jq == 'undefined') {
         var jq = window.parent.frames["telaPrincipal"].jQuery;
      }
      for (i=1;i<4;i++) {
         jq('div#containerPopUp',parent.frames[i].document).remove();
         jq('html',parent.frames[i].document).css({'overflow':'auto'});
      }
   }

   function bloqueiaFrames()
   {
    stHTMLFrames = '<div id="containerPopUp"></div>';
    var containerCSS = { 'width':'100%',
                         'height': '100%',
                         'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                         'position':'absolute',
                         'left':'0',
                         'top':'0' };

    for (i=1;i<4;i++) {
        jq('html',parent.frames[i].document).append(stHTMLFrames);
        jq('html',parent.frames[i].document).css({'overflow':'hidden'});
        jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
    }
   }

   jq(function () {
      jq( "#dialog" ).dialog({
         width  : '210px',
         minHeight : '50px',
         closeText : 'X',
         autoOpen: false,
         modal: true,
         buttons: [ { text: "OK", click:
            function () {
               var boValida = /^((0?[1-9]|[12]\d)\/(0?[1-9]|1[0-2])|30\/(0?[13-9]|1[0-2])|31\/(0?[13578]|1[02]))\/(19|20)\d{2}$/;
               if (boValida.test(jq("#stDataReserva").val())) {
                  jq(this).dialog('close');
                  window.parent.frames['telaMenu'].location = 'menu.php?'+'<?=Sessao::getId()?>'+'&nivel=1&cod_gestao_pass=2&stTitulo=Financeira&stDataReserva='+jq("#stDataReserva").val();
                  desbloqueiaFrames();
               } else {
                  alert('Digite uma data válida no formato "dd/mm/yyyy"');
               }
            }
         } ]

      });
   });

   jq(document).ready(function () {
      //jq('#dialog').css('display','block');
      jq('#dialog').dialog('open');
      bloqueiaFrames();
   });
</script>
<?php
}

//Acha caminho principal do siam web

$sDir  = dirname($_SERVER["SCRIPT_NAME"]);
Sessao::write('raiz', $sDir."/");
