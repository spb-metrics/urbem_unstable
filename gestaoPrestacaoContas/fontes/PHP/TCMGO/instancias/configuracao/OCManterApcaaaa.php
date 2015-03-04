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

    * Página de Formulário para configuração
    * Data de Criação   : 30s/04/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: OCManterApcaaaa.php 61679 2015-02-25 13:07:38Z evandro $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOBalancoApcaaaa.class.php');
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$arContas = Sessao::read('arContas');
$arExcluidas = Sessao::read('arExcluidas');

function montaLista($arConta)
{
    
    $rsConta = new RecordSet();
    if ( is_array($arConta) ) {
        $rsConta->preenche( $arConta );
    } else {
       $rsConta->preenche( array() );
    }

    $obTable = new Table();
    $obTable->setRecordSet( $rsConta );
    $obTable->setSummary('Lista de Contas');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Código', 10 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_plano', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s)', array( 'id','tipo_lancamento' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs .= "document.getElementById('spnContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

switch ($stCtrl) {
    case 'buscaEstrutural' :
        if ($_REQUEST['inCodConta'] == '') {
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "alertaAviso('@Conta inválida!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
        $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
        $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
        $stFiltro.= " AND pc.cod_estrutural LIKE '1.%' ";
        $stFiltro.= " AND NOT EXISTS ( SELECT 	1
                                                FROM 	tcmgo.balanco_apcaaaa
                                               WHERE    balanco_apcaaaa.cod_plano = pa.cod_plano
                                                 AND    balanco_apcaaaa.exercicio = pa.exercicio
                                            ) ";
        $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );
        if ( $rsConta->getNumLinhas() > 0 ) {
            $stJs .= "document.getElementById('stConta').innerHTML = '".$rsConta->getCampo('nom_conta')."'; ";
        } else {
            $stJs.= "alertaAviso('@Código estrutural inválido!', 'form','erro','".Sessao::getId()."');";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        }
        break;
    
    case 'incluirConta' :
        $arContas = Sessao::read('arContas');
        if ($_REQUEST['inCodConta'] == '') {
            $stMensagem = 'Conta inválida!';
        }

        if ($_REQUEST['inTipoLancamento'] == '') {
            $stMensagem = 'Preencha o tipo do lançamento!';
        }

        if ( count( $arContas['arContas_'.$_REQUEST['inTipoLancamento']] ) > 0 ) {
            foreach ($arContas['arContas_'.$_REQUEST['inTipoLancamento']] as $arAux) {
                if ( $arAux['cod_plano'] == $_REQUEST['inCodConta'] ) {
                        $stMensagem = 'Esta conta já consta na lista!';
                        break;
                }
            }
        }

        if (!$stMensagem) {            
            
            $inCount = count($arContas['arContas_'.$_REQUEST['inTipoLancamento']]);

            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );
            
            for($i=0; $i <= $inCount; $i++){                                                
                if ( $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['id'] === $i ) {
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['id']              = $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['id'];
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_estrutural']  = $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_estrutural'];
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_plano']       = $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_plano'];
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['nom_conta']       = $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['nom_conta'];
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['tipo_lancamento'] = $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$i]['tipo_lancamento'];
                } else {                    
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['id']              = $inCount;
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_estrutural']  = $rsConta->getCampo('cod_estrutural');
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['cod_plano']       = $rsConta->getCampo('cod_plano');
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['nom_conta']       = $rsConta->getCampo('nom_conta');
                    $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']][$i]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                }                       
            }            
            Sessao::write('arContas', $arContasAux);
       
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= montaLista( $arContasAux['arContas_'.$_REQUEST['inTipoLancamento']] );
            
            if (count($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']]) > 0) {
                foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']] as $arAux) {
                    if ( $arAux['cod_plano'] != $rsConta->getCampo('cod_plano') ) {
                        $arTemp[] = $arAux;
                    }
                }
                $arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']] = $arTemp;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
        break;
    
    case 'excluirListaItens' :
        $i=0;
        foreach ($arContas['arContas_'.$_REQUEST['tipo_lancamento']] as $arAux) {                        
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;

            } else {
                $arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento']][count($arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento']])] = $arAux;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }
        $arContas['arContas_'.$_REQUEST['tipo_lancamento']] = $arTemp;        
        Sessao::write('arContas', $arContas);
        $stJs .= montaLista( $arContas['arContas_'.$_REQUEST['tipo_lancamento']] );
        break;
    
    case 'preencheLista' :
        if ($_REQUEST['inTipoLancamento'] != '') {
            $obTTGOBalancoApcaaaa = new TTGOBalancoApcaaaa();
            $obTTGOBalancoApcaaaa->setDado( 'exercicio', Sessao::getExercicio() );
            $obTTGOBalancoApcaaaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
            $obTTGOBalancoApcaaaa->recuperaRelacionamento( $rsContas );
            while ( !$rsContas->eof() ) {
                $boExiste = false;
                if ( count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'] ]) > 0) {
                    foreach ($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'] ] as $arAux) {
                        if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                            $boExiste = true;

                        }
                    }
                }
                if ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'] ]) {
                    foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento']] as $arAux2) {
                        if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                            $boExiste = true;
                        }
                    }
                }
                if (!$boExiste) {
                    $inCount = count($arContas['arContas_'.$_REQUEST['inTipoLancamento']]);
                    $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$inCount]['id'] = $inCount;
                    $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                    $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                    $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                    $arContas['arContas_'.$_REQUEST['inTipoLancamento']][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                }
                $rsContas->proximo();
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs .= montaLista( $arContas['arContas_'.$_REQUEST['inTipoLancamento']] );
        break;
}
echo $stJs;

?>
