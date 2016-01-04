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

$stCtrl = $request->get('stCtrl');

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
    $obTable->Head->addCabecalho( 'Tipo de natureza', 10 );
    $obTable->Head->addCabecalho( 'Descrição', 40 );

    $obTable->Body->addCampo( 'cod_bem', 'C' );
    $obTable->Body->addCampo( 'classificacao', 'C' );
    $obTable->Body->addCampo( '[codigo_tipo] - [descricao_natureza]', 'C' );
    $obTable->Body->addCampo( 'descricao', 'E' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_PAT_INSTANCIAS."bem/".$pgOcul."?".Sessao::getId()."&id=%s', 'excluirBem' );", array( 'cod_bem' ) );

    $obTable->montaHTML( true );

    return "$('spnBem').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'incluirBaixaBem':
        
        if ($request->get('inTipoBaixa') == '') {
            $stMensagem = 'Prencha o campo Tipo de baixa.';
        }
        if ($request->get('inCodBemInicio') == '') {
            $stMensagem = 'Prencha o campo Bem Inicial.';
        }
        if ($request->get('inCodBemInicio') != '' AND $request->get('inCodBemFim') != '' AND $request->get('inCodBemInicio') > $request->get('inCodBemFim')) {
            $stMensagem = 'O campo Bem Final não pode ser superior ao Inicial.';
        }
        
        if ($stMensagem == '') {
            $obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();

            //se passar somente o inCodBemFim, faz um between, se nao, traz so um registro
            if ($request->get('inCodBemFim') != '') {
                $stFiltro = " \n WHERE bem.cod_bem BETWEEN ".$request->get('inCodBemInicio')." AND ".$request->get('inCodBemFim')." \n ";
            } else {
                $obTPatrimonioBemBaixado->setDado( 'cod_bem', $request->get('inCodBemInicio') );
            }
            
            //recupera de acordo com o filtro
            $obTPatrimonioBemBaixado->recuperaRelacionamento( $rsBem, $stFiltro );
            
            if ( $request->get('inTipoBaixa') == 1 || $request->get('inTipoBaixa') == 3 || $request->get('inTipoBaixa') == 5) {
                // verifica se o bem pertence a configuração de bens Imóveis
                $stTipoBaixa = "Imóvel";
                $inTipoBaixa = 2;
            } else if ( $request->get('inTipoBaixa') == 2 || $request->get('inTipoBaixa') == 4 || $request->get('inTipoBaixa') == 6) {
                // verifica se o bem pertence a configuração de bens Móveis
                $stTipoBaixa = "Móvel";
                $inTipoBaixa = 1;
            }
            
            //loop para preencher a sessao com os bens selecionados
            $arBens = Sessao::read('bens');
            $inCount = count( $arBens );
                                    
            while ( !$rsBem->eof() ) {
                $boRepetido       = false;
                $boOutroTipoBaixa = false;
                
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

                // se foi selecionado um tipo de baixa, verifica se o bem pertence ao mesmo tipo de baixa selecionado.
                if ( $request->get('inTipoBaixa') != "" && $request->get('inTipoBaixa') != 0 && $inTipoBaixa != $rsBem->getCampo('codigo')) {
                    // caso o tipo de baixa seja diferente do selecionado, não entra na lista de baixa.
                    $arBemOutroTipo[] = $rsBem->getCampo('cod_bem');
                    $boOutroTipoBaixa = true;
                } 
                
                // Monta lista dos bens disponiveis para baixa.
                if ( !$boRepetido && $rsBem->getCampo('status') != 'baixado' && !$boOutroTipoBaixa) {
                    $arBens[$inCount]['id']                 = $inCount;
                    $arBens[$inCount]['cod_bem']            = $rsBem->getCampo( 'cod_bem' );
                    $arBens[$inCount]['classificacao']      = $rsBem->getCampo( 'cod_natureza' ).'.'.$rsBem->getCampo( 'cod_grupo' ).'.'.$rsBem->getCampo( 'cod_especie' );
                    $arBens[$inCount]['descricao']          = $rsBem->getCampo( 'descricao' );
                    $arBens[$inCount]['codigo_tipo']        = $rsBem->getCampo( 'codigo' );
                    $arBens[$inCount]['descricao_natureza'] = $rsBem->getCampo( 'descricao_natureza' );
                    $inCount++;
                }
                
                $rsBem->proximo();
            }
            
            // Monta avisos, caso houver alguma inconsistencia
            if ( is_array( $arBemBaixado ) && is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemBaixado).") já estão baixados e os bens (".implode(',',$arRepetido).") já estão na lista.','form','erro','".Sessao::getId()."');";
            } elseif ( is_array( $arBemBaixado ) && is_array( $arBemOutroTipo ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemBaixado).") já estão baixados e os bens (".implode(',',$arBemOutroTipo).") não pertencem ao mesmo tipo de natureza ".$stTipoBaixa." selecionado para a baixa.','form','erro','".Sessao::getId()."');";
            } elseif ( !is_array( $arBemBaixado ) && is_array( $arBemOutroTipo ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemOutroTipo).") não pretencem ao mesmo tipo de natureza selecionada para baixa.','form','erro','".Sessao::getId()."');";
            } elseif ( is_array( $arBemBaixado ) && !is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arBemBaixado).") já estão baixados.','form','erro','".Sessao::getId()."');";
            } elseif ( !is_array( $arBemBaixado ) && is_array( $arRepetido ) ) {
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

?>