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
  * Data de criação : 23/05/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.04.08

    $Id: OCManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(TCOM."TComprasConfiguracao.class.php");

function montSpanResponsaveis()
{
    $rsRecordSet = new RecordSet;

    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( count ( $arResponsaveisEntidades ) > 0 ) {
        $rsRecordSet->preenche(  $arResponsaveisEntidades );
    }

    $obLista = new Lista;

    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Responsáveis pelos Departamentos de Compras das Entidades');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código Entidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento ( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delResp');" );
    $obLista->ultimaAcao->addCampo("","&inId=[inId]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnResponsaveis').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnResponsaveis').innerHTML = '".$html."';\n";

    return $stJs;

}

function addResponsavel($inCodCGM, $inCodEntidade, $stNomCGM)
{
    $stJs = '';

    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( is_array($arResponsaveisEntidades) ) {
        $stErro = '';
        foreach ($arResponsaveisEntidades as $registro) {
            if ($registro['cod_entidade'] == $inCodEntidade) {
                $stErro = 'Já existe um responsável para esta entidade.';
            }
        }
    }

    if ($stErro) {

        $stJs = "alertaAviso('$stErro','form','erro','".Sessao::getId()."');\n  ";

    } else {
        $inUltimoCodigoResp = Sessao::read('inUltimoCodigoResp');
        $arRegistro = array();
        $arRegistro['cod_modulo']   = 35;
        $arRegistro['parametro']    = 'responsavel';
        $arRegistro['valor']        = $inCodCGM;
        $arRegistro['exercicio']    = Sessao::getExercicio();
        $arRegistro['cod_entidade'] = $inCodEntidade;
        $arRegistro['nom_cgm']      = $stNomCGM;
        $arRegistro['inId']         = $inUltimoCodigoResp++;
        $arResponsaveisEntidades[] = $arRegistro;

        Sessao::write('arResponsaveisEntidades' , $arResponsaveisEntidades);
        $stJs = montSpanResponsaveis();
    }

    return $stJs;

}

function delResponsavel($inId)
{
    $arResponsaveisEntidades = Sessao::read('arResponsaveisEntidades');

    if ( count ($arResponsaveisEntidades) > 0 ) {

        foreach ($arResponsaveisEntidades as $registro) {

            if ($registro['inId'] == $inId) {
                $arExclusoes[] = $registro;
            } else {
                $arResps[] = $registro;
            }
        }

        $arResponsaveisEntidades = $arResps;
        $arResponsaveisEntidadesExcluidos = $arExclusoes;

        Sessao::write('arResponsaveisEntidades', $arResponsaveisEntidades);
        Sessao::write('arResponsaveisEntidadesExcluidos', $arResponsaveisEntidadesExcluidos);

        $stJs = montSpanResponsaveis();
    }

    return $stJs;

}

switch ($_REQUEST['stCtrl']) {
    case "recuperaFormularioAlteracao":

    break;
    case 'incluirResponsavel':

      $stJs = addResponsavel( $_REQUEST['inCGM'] , $_REQUEST['inCodEntidade'], $_REQUEST['stNomCGM'] ) ;

    break;

    case 'delResp':
        $stJs = delResponsavel( $_REQUEST['inId'] );
    break;

}

if ($stJs) {
    echo $stJs;
}
