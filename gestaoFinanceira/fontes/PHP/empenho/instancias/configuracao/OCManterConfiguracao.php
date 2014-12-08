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
  * Formulário oculto
  * Data de criação :

    * @author Analista:
    * @author Programador:

    $Revision: 31968 $
    $Name$
    $Author: vitor $
    $Date: 2007-07-23 11:35:09 -0300 (Seg, 23 Jul 2007) $

    Caso de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.3  2007/07/23 14:35:09  vitor
Bug#9669#

Revision 1.2  2007/07/13 19:05:13  cako
Bug#9383#, Bug#9384#

Revision 1.1  2007/07/03 15:30:42  luciano
Bug#9451#

*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaSpanContaCaixa()
{
    $rsRecordSet = new RecordSet;

    if ( count ( Sessao::read('arItens') ) > 0 ) {
        $rsRecordSet->preenche( Sessao::read('arItens') );
    }

    $obLista = new Lista;

    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Conta Caixa das Entidades');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código Entidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    /*
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->commitCabecalho();
    */
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Caixa");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodEntidade" );
    $obLista->commitDado();
    /*
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_entidade" );
    $obLista->commitDado();
    */
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodConta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'ESQUERDA' );
    $obLista->ultimoDado->setCampo( "stNomConta" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delContaCaixa');" );
    //$obLista->ultimaAcao->addCampo("1","inId");
    $obLista->ultimaAcao->addCampo("","&inId=[inId]&inCodPlano=[inCodConta]&inCodEntidade=[inCodEntidade]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnContaCaixa').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnContaCaixa').innerHTML = '".$html."';\n";

    return $stJs;

}

function addContaCaixa($inCodEntidade, $stNomEntidade, $inCodContaAnalitica, $stNomContaAnalitica)
{
    $stJs = '';

    $arItens = Sessao::read('arItens');
    if ( is_array($arItens) ) {
        $stErro = '';
        foreach ($arItens as $registro) {
            if ($registro['inCodEntidade'] == $inCodEntidade) {
                $stErro = 'Já existe uma conta caixa para esta entidade.';
            }
        }
    }

    if ($stErro) {

        $stJs = "alertaAviso('$stErro','form','erro','".Sessao::getId()."');\n  ";

    } else {

        $inId = count(Sessao::read('arItens'));

        $arItens[$inId]['inId'            ] = $inId;
        $arItens[$inId]['inCodEntidade'   ] = $inCodEntidade;
        $arItens[$inId]['inCodConta'      ] = $inCodContaAnalitica;
        $arItens[$inId]['stNomConta'      ] = $stNomContaAnalitica;

        Sessao::write('arItens', $arItens);

        $stJs = montaSpanContaCaixa();
    }

    return $stJs;

}

switch ($_REQUEST['stCtrl']) {

   case 'limpaPopUpContaAnalitica':

      $stJs .= "document.frm.inCodContaAnalitica.value = '';\n";
      $stJs .= "document.getElementById('stNomContaAnalitica').innerHTML = '&nbsp';\n";
      echo $stJs;

    break;

    case "recuperaFormularioAlteracao":

    break;
    case 'incluircontaCaixa':

      $stJs = addContaCaixa( $_REQUEST['inCodEntidade'], $_REQUEST['stNomEntidade'], $_REQUEST['inCodContaAnalitica'], $_REQUEST['stNomContaAnalitica'] ) ;

    break;

    case 'delContaCaixa':
        if ($_REQUEST['inCodPlano']) {
            include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoConfiguracao.class.php");
            $obErro = new Erro();
            $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
            $obTEmpenhoConfiguracao->setDado('cod_plano', $_REQUEST['inCodPlano']);
            $obTEmpenhoConfiguracao->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTEmpenhoConfiguracao->setDado('exercicio', Sessao::getExercicio() );
            $obErro = $obTEmpenhoConfiguracao->verificaUtilizacaoContaCaixa( $rsRecordSet );
            if (!$obErro->ocorreu() && $rsRecordSet->getNumLinhas() < 0) {
                $arTMP = array();
                $id = $_REQUEST['inId'];
                $inCount = 0;

                $arItensSessao = array();
                $arItensSessao = Sessao::read('arItens');
                $arItens = array();
                foreach ($arItensSessao as $array) {

                    if ($array['inId'] != $id) {

                        $arItens[$inCount]['inId'            ] = $array['inId'];
                        $arItens[$inCount]['inCodEntidade'   ] = $array['inCodEntidade'];
                        $arItens[$inCount]['inCodConta'      ] = $array['inCodConta'   ];
                        $arItens[$inCount]['stNomConta'      ] = $array['stNomConta'   ];

                        $inCount = $inCount + 1;
                    }
                    Sessao::write('arItens', $arItens);
                    $stJs = montaSpanContaCaixa();
                }
            } else {
                $stJs = "alertaAviso('Erro ao excluir conta: A Conta Caixa ".$_REQUEST['inCodPlano']." já possui movimentação de Retenções.','form','erro','".Sessao::getId()."');\n  ";
            }
        }

    break;

}

if ($stJs) {
    echo $stJs;
}
