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
    * Data de Criação: 18/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25675 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-27 09:57:24 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/09/27 12:57:24  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php");
include_once( CAM_GP_PAT_COMPONENTES."IPopUpBem.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixarBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaBens($arBens)
{
    global $pgOcul;
    $rsBens = new RecordSet();
    $rsBens->preenche( $arBens );

    $obTable = new Table();
    $obTable->setRecordset( $rsBens );
    $obTable->setSummary( 'Lista de Bens' );

    $obTable->Head->addCabecalho( 'Código', 10 );
    $obTable->Head->addCabecalho( 'Classificação', 20 );
    $obTable->Head->addCabecalho( 'Descrição', 50 );

    $obTable->Body->addCampo( 'cod_bem', 'C' );
    $obTable->Body->addCampo( 'classificacao', 'C' );
    $obTable->Body->addCampo( 'descricao', 'E' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_PAT_INSTANCIAS."bem/".$pgOcul."?".Sessao::getId()."&id=%s', 'excluirBem' );", array( 'cod_bem' ) );

    $obTable->montaHTML( true );

    return "$('spnBem').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'incluirBaixaBem':
        if ($_REQUEST['inCodBemInicio'] == '') {
            $stMensagem = 'Prencha o campo Bem Inicial.';
        }
        if ($_REQUEST['inCodBemInicio'] != '' AND $_REQUEST['inCodBemFim'] != '' AND $_REQUEST['inCodBemInicio'] > $_REQUEST['inCodBemFim']) {
            $stMensagem = 'O campo Bem Final não pode ser superior ao Inicial.';
        }
        if ($stMensagem == '') {
            $obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();

            //se passar somente o inCodBemFim, faz um between, se nao, traz so um registro
            if ($_REQUEST['inCodBemFim'] != '') {
                $stFiltro = "
                    WHERE bem.cod_bem BETWEEN ".$_REQUEST['inCodBemInicio']." AND ".$_REQUEST['inCodBemFim']."
                ";
            } else {
                $obTPatrimonioBemBaixado->setDado( 'cod_bem', $_REQUEST['inCodBemInicio'] );
            }
            //recupera de acordo com o filtro
            $obTPatrimonioBemBaixado->recuperaRelacionamento( $rsBem, $stFiltro );
            //loop para preencher a sessao com os bens selecionados
            $arBens = Sessao::read('bens');
            $inCount = count( $arBens );
            while ( !$rsBem->eof() ) {
                $boRepetido = false;
                if ( is_array( $arBens ) ) {
                    foreach ($arBens as $arTEMP) {
                        if ( $arTEMP['cod_bem'] == $rsBem->getCampo('cod_bem') ) {
                            $arRepetido[] = $arTEMP['cod_bem'];
                            $boRepetido = true;
                        }
                    }
                }
                if ( $rsBem->getCampo('status') == 'baixado' ) {
                    $arBemBaixado[] = $rsBem->getCampo('cod_bem');
                }

                if ( !$boRepetido AND $rsBem->getCampo('status') != 'baixado' ) {
                    $arBens[$inCount]['id'] = $inCount;
                    $arBens[$inCount]['cod_bem'] = $rsBem->getCampo( 'cod_bem' );
                    $arBens[$inCount]['classificacao'] = $rsBem->getCampo( 'cod_natureza' ).'.'.$rsBem->getCampo( 'cod_grupo' ).'.'.$rsBem->getCampo( 'cod_especie' );
                    $arBens[$inCount]['descricao'] = $rsBem->getCampo( 'descricao' );
                    $inCount++;
                }
                $rsBem->proximo();
            }
            //se algum bem selecionado ja estava baixado, mostra um aviso
            if ( is_array( $arBemBaixado ) AND is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemBaixado).") já estão baixados e os bens (".implode(',',$arRepetido).") já estão na lista.','form','erro','".Sessao::getId()."');";
            } elseif ( is_array( $arBemBaixado ) AND !is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemBaixado).") já estão baixados.','form','erro','".Sessao::getId()."');";
            } elseif ( !is_array( $arBemBaixado ) AND is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arRepetido).") já estão na lista.','form','erro','".Sessao::getId()."');";
            }

            Sessao::write('bens',$arBens);

            //monta a lista
            $stJs.= "$('inCodBemInicio').value = '';";
            $stJs.= "$('inCodBemFim').value = '';";
            $stJs.= "$('stNomBemInicio').innerHTML = '&nbsp;';";
            $stJs.= "$('stNomBemFim').innerHTML = '&nbsp;';";
            $stJs.= montaListaBens( $arBens );
        } else {
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
        break;
    case 'excluirBem' :
        $arBem = Sessao::read('bens');
        for ( $i = 0; $i < count( $arBem ); $i++ ) {
            if ($arBem[$i]['cod_bem'] != $_REQUEST['id']) {
                $arTEMP[] = $arBem[$i];
            }
        }

        Sessao::write('bens',$arTEMP);

        $stJs = montaListaBens( ( count($arTEMP) > 0 ) ? $arTEMP : array() );

        break;
    case 'limparBens' :
        Sessao::remove('bens');
        break;
}

echo $stJs;
