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
    * Pagina de Oculto
    * Data de Criação   : 29/01/2007

    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * Casos de uso: uc-03.04.33

    $Id: OCManterCompraDireta.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

unset($stJs);

function montaListaItens($rsItens)
{
    // formata recordset
    $rsItens->setPrimeiroElemento();

    $rsItens->addFormatacao('valor_unitario'      , 'NUMERIC_BR');
    $rsItens->addFormatacao('quantidade'          , 'NUMERIC_BR_4');
    $rsItens->addFormatacao('valor_total_real'    , 'NUMERIC_BR');
    $rsItens->addFormatacao('valor_ultima_compra' , 'NUMERIC_BR');

    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

    $table = new Table();

    $table->setRecordset( $rsItens );
    $table->setSummary('Itens');
    //$table->setConditional( true , "#ddd" );

    $table->Head->addCabecalho( 'Item' , 40  );
    $table->Head->addCabecalho( 'Centro de Custo', 25 );
    $table->Head->addCabecalho( 'Valor de Referência' , 10  );
    $table->Head->addCabecalho( 'Valor da Última Compra' , 10  );
    $table->Head->addCabecalho( 'Qtde' , 10  );
    $table->Head->addCabecalho( 'Valor Total' , 10  );

    $table->Body->addCampo( '[cod_item] - [descricao_completa]. [complemento]' , 'E');
    $table->Body->addCampo( '[cod_centro] - [centro_custo_descricao]' , 'E');
    $table->Body->addCampo( 'valor_unitario' , 'D');
    $table->Body->addCampo( 'valor_ultima_compra' , 'D');
    $table->Body->addCampo( 'quantidade_real' , 'D' );
    $table->Body->addCampo( 'valor_total_real' , 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('spnItens').innerHTML = '" . $stHTML . "';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {

    case 'validaDatas':
        if ($_REQUEST['stDataEntregaProposta'] && $_REQUEST['stDataValidade']) {
            $arDataEntrega = explode('/',$_REQUEST['stDataEntregaProposta']);
            $arDataValidade = explode('/',$_REQUEST['stDataValidade']);

            $inDataEntrega  = $arDataEntrega[2] . $arDataEntrega[1] . $arDataEntrega[0];
            $inDataValidade = $arDataValidade[2] . $arDataValidade[1] . $arDataValidade[0];

            if ($inDataEntrega > $inDataValidade) {
                $stJs = "alertaAviso('Data de Validade da Proposta deve ser posterior à Data de Entrega.' , 'form' , 'erro' , '" . Sessao::getId() . "' , '../');\n";
            }
        }
    break;

    case 'montaClusterLabels':

        $stErro = FALSE;
        list ($inCodMapa , $stExercicioMapa) = explode( '/' , $_REQUEST['stMapaCompras']);

        if ($inCodMapa && $stExercicioMapa) {
            $obFormulario = new Formulario;
            require_once CAM_GP_LIC_COMPONENTES.'IClusterLabelsMapa.class.php';
            $obCluster = new IClusterLabelMapa ( $obForm , $inCodMapa , $stExercicioMapa );
            $obCluster->boDispensa = TRUE;
            $obCluster->geraFormulario ( $obFormulario );

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();
        }

        $stJs  = "d.getElementById('spnLabels').innerHTML = '" . $stHtml . "';\n";
    break;

    case 'montaItens' :
        // captura mapa do request;
        list( $inCodMapa , $stExercicioMapa ) = explode ('/' , $_REQUEST['stMapaCompras']);
        $stExercicioMapa = ( $stExercicioMapa == '' ) ? Sessao::getExercicio() : $stExercicioMapa;

        if ($inCodMapa != '' && $stExercicioMapa == Sessao::getExercicio()) {
            // verificar se esta em licitação
            $stFiltro =  "WHERE  cod_mapa = ".$inCodMapa."
                            AND  exercicio_mapa = '".$stExercicioMapa."'
                            AND  NOT EXISTS (
                                             SELECT  1
                                               FROM  licitacao.licitacao_anulada
                                              WHERE  licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                                AND  licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                                AND  licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                                AND  licitacao_anulada.exercicio      = licitacao.exercicio
                                           )";

            $boLicitacao = SistemaLegado::pegaDado("cod_licitacao","licitacao.licitacao", $stFiltro);

            $stFiltro = " WHERE  cod_mapa = ".$inCodMapa."
                            AND  exercicio_mapa = '".$stExercicioMapa."'
                            AND  NOT EXISTS(
                                            SELECT  1
                                              FROM  compras.compra_direta_anulacao
                                             WHERE  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                                               AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                               AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                               AND  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                          ) ";

            $boCompraDireta = SistemaLegado::pegaDado('cod_compra_direta','compras.compra_direta', $stFiltro );

              include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';

            $obTComprasMapa = new TComprasMapa;

            $stFiltro  = " AND mapa.cod_mapa = ".$inCodMapa;
            $stFiltro .= " AND mapa.exercicio = '".$stExercicioMapa."'";

            $obTComprasMapa->recuperaMapaProcessoLicitatorio($rsComprasMapa, $stFiltro);

            if (($boLicitacao || $boCompraDireta || ($rsComprasMapa->getNumLinhas() <= 0 ))) {
                $stJs  = "$('spnItens').innerHTML= '';\n";
                $stJs .= "$('stTotalMapa').innerHTML = '';\n";
            } else {
                include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
                include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCatalogoItem.class.php';

                $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
                $obTComprasMapaItem = new TComprasMapaItem();

                $obTComprasMapaItem->setDado( 'exercicio', "'".$stExercicioMapa."'" );
                $obTComprasMapaItem->setDado( 'cod_mapa', $inCodMapa );
                $obTComprasMapaItem->recuperaItensCompraDireta( $rsMapaItens );

                // somar total do mapa
                $nuTotal = 0.00;
                while ( !$rsMapaItens->eof() ) {
                    $nuTotal += ($rsMapaItens->getCampo('valor_total') - $rsMapaItens->getCampo('vl_total_anulado'));

                    // Recupera o valor da última compra do ítem.
                    $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
                    $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicioMapa);
                    $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

                    $rsMapaItens->setCampo ( 'valor_ultima_compra' , $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra') );

                    $rsMapaItens->proximo();
                }
                $nuTotal = number_format($nuTotal,2,',','.');

                $rsMapaItens->setPrimeiroElemento();
                $stJs  = "$('stTotalMapa').innerHTML = '" . $nuTotal . "';\n";
                $stJs .= "$('lblObjeto').innerHTML = '".$rsMapaItens->getCampo('cod_objeto')." - ".nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsMapaItens->getCampo('objeto_descricao'))))."';\n";
                $stJs .= "$('hdnObjeto').value = '".$rsMapaItens->getCampo('cod_objeto')."';\n";
                $stJs .= montaListaItens( $rsMapaItens ) ;
            }
        }
    break;

    case 'montaItensAlterar':

        list($inCodMapa , $stExercicioMapa) = explode('/' , $_REQUEST['stMapaCompras']);
        $stExercicioMapa = ($stExercicioMapa == '') ? Sessao::getExercicio() : $stExercicioMapa;
        $boExecuta = false;
        if (($_REQUEST['hdnMapaCompras'] == ($inCodMapa.'/'.$stExercicioMapa)) || $_REQUEST['boAlteraAnula']) {
            $boExecuta = true;
        } else {
            if ($_REQUEST['stMapaCompras'] != '') {
                $boLicitacao = SistemaLegado::pegaDado ( "cod_licitacao","licitacao.licitacao"," where cod_mapa = " . $inCodMapa  . " and exercicio_mapa = " . $stExercicioMapa . "");
                $boCompraDireta = SistemaLegado::pegaDado ('cod_compra_direta','compras.compra_direta',' where cod_mapa = '.$inCodMapa.' and exercicio_mapa = '.$stExercicioMapa.' ' );

                include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';
                $obTComprasMapa = new TComprasMapa;
                $stFiltro .= " AND  mapa.cod_mapa  = ".$inCodMapa;
                $stFiltro .= " AND  mapa.exercicio = '".$stExercicioMapa."'";

                $obTComprasMapa->recuperaMapaProcessoLicitatorio($rsComprasMapa, $stFiltro);

                if (!$boLicitacao && !$boCompraDireta && ($rsComprasMapa->getNumLinhas() >= 1)) {
                    $boExecuta = true;
                }
            }

        }

        if ($boExecuta) {
            include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
            include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCatalogoItem.class.php';

            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
            $obTComprasMapaItem = new TComprasMapaItem();

            $obTComprasMapaItem->setDado( 'exercicio', $stExercicioMapa );
            $obTComprasMapaItem->setDado( 'cod_mapa', $inCodMapa );

            $obTComprasMapaItem->recuperaItensCompraDireta( $rsMapaItens );
            // somar total do mapa
            $nuTotal = 0.00;

            while ( !$rsMapaItens->eof() ) {
                $nuTotal += ($rsMapaItens->getCampo('valor_total') - $rsMapaItens->getCampo('vl_total_anulado'));

                // Recupera o valor da última compra do ítem.
                $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
                $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicioMapa);
                $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

                $rsMapaItens->setCampo ( 'valor_ultima_compra' , $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra') );

                $rsMapaItens->proximo();
            }
            $nuTotal = number_format($nuTotal,2,',','.');

            $rsMapaItens->setPrimeiroElemento();

            $stJs  = "$('stTotalMapa').innerHTML = '" . $nuTotal . "';\n";
            $stJs .= "$('lblObjeto').innerHTML = '".$rsMapaItens->getCampo('cod_objeto')." - ".nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsMapaItens->getCampo('objeto_descricao'))))."';\n";
            $stJs .= "$('hdnObjeto').value = '".$rsMapaItens->getCampo('cod_objeto')."';\n";
            $stJs .= montaListaItens( $rsMapaItens ) ;

        } else {
            $stJs  = "$('spnItens').innerHTML= '';\n";
            $stJs .= "$('stTotalMapa').innerHTML = '';\n";
        }

    break;

    case 'validaMapa':
        if ($_REQUEST['stMapaCompras'] != '' and $_REQUEST['stDtCompraDireta'] != '') {
            $arMapa = array();
            $arMapa = explode('/',$_REQUEST['stMapaCompras']);

            include ( CAM_GP_COM_MAPEAMENTO."TComprasMapaSolicitacao.class.php" );
            $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;
            $obTComprasMapaSolicitacao->setDado( 'cod_mapa'  , $arMapa[0]);
            $obTComprasMapaSolicitacao->setDado( 'exercicio' ,  Sessao::getExercicio() );
            $obTComprasMapaSolicitacao->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTComprasMapaSolicitacao->recuperaMaiorDataSolicitacaoMapa($rsRecordSet);

            if ($rsRecordSet->getNumLinhas() > 0) {
                if (!SistemaLegado::comparaDatas($_REQUEST['stDtCompraDireta'],$rsRecordSet->getCampo('dt_solicitacao'),true)) {
                   $stJs .= "alertaAviso( 'A data da Compra Direta deve ser igual ou maior do que a maior data das solicitações do mapa (".$rsRecordSet->getCampo('dt_solicitacao').").','form','erro','".Sessao::getId()."');";
                   $stJs .= "f.stDtCompraDireta.value='';";
                   $stJs .= "f.stDtCompraDireta.focus();";
                }
            }
        }
    break;

    case 'validaDtCompraDireta':
        if ($_REQUEST['stDtCompraDireta'] != '') {
            // Não pode ser maior que a data corrente.
            if (SistemaLegado::comparaDatas( $_REQUEST['stDtCompraDireta'] , date('d/m/Y'))) {
               $stJs .= "alertaAviso( 'A Data da Compra Direta (".$_REQUEST['stDtCompraDireta'].") não pode ser maior que a data atual (".date('d/m/Y').")!','form','erro','".Sessao::getId()."');";
               $stJs .= "f.stDtCompraDireta.value='';";
               $stJs .= "f.stDtCompraDireta.focus();";
            }
        }

        if ($_REQUEST['stMapaCompras'] != '' and $_REQUEST['stDtCompraDireta'] != '') {
            $arMapa = array();
            $arMapa = explode('/',$_REQUEST['stMapaCompras']);

            include ( CAM_GP_COM_MAPEAMENTO."TComprasMapaSolicitacao.class.php" );
            $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;
            $obTComprasMapaSolicitacao->setDado( 'cod_mapa'  , $arMapa[0]);
            $obTComprasMapaSolicitacao->setDado( 'exercicio' , $arMapa[1]);
            $obTComprasMapaSolicitacao->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTComprasMapaSolicitacao->recuperaMaiorDataSolicitacaoMapa($rsRecordSet);

            if ($rsRecordSet->getNumLinhas() > 0) {
                if (!SistemaLegado::comparaDatas($_REQUEST['stDtCompraDireta'],$rsRecordSet->getCampo('dt_solicitacao'),true)) {
                   $stJs .= "alertaAviso( 'A data da Compra Direta deve ser igual ou maior do que a maior data das solicitações do mapa (".$rsRecordSet->getCampo('dt_solicitacao').").','form','erro','".Sessao::getId()."');";
                   $stJs .= "f.stDtCompraDireta.value='';";
                   $stJs .= "f.stDtCompraDireta.focus();";
                }
            }
        }
    break;

    case 'recuperaUltimaDataContabil' :

        include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $stJs = isset($stJs) ? $stJs : null;

        $stFiltro = "      AND  empenho.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
        $stFiltro.= "      AND  empenho.exercicio = '".Sessao::getExercicio()."'      \n";
        $stOrdem  = " ORDER BY  empenho.dt_empenho DESC LIMIT 1                       \n";

        $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

        if (isset($dataUltimoEmpenho) && ($dataUltimoEmpenho != "")) {
            $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));
        }

        /*
            Rotina que serve para preencher a data da compra direta com
            a última data do lançamento contábil.
        */
        $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']);
        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

        if (($rsMaiorData->getCampo( "data_autorizacao" ) !="") ) {
            $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
            $stExercicioDtAutorizacao = substr($stDtAutorizacao, 6, 4);
        } elseif ( ( $dataUltimoEmpenho !="") ) {
            $stDtAutorizacao = $dataUltimoEmpenho;
            $stExercicioDtAutorizacao = substr($dataUltimoEmpenho, 6, 4);
        } else {
            $stDtAutorizacao = "01/01/".Sessao::getExercicio();
            $stExercicioDtAutorizacao = Sessao::getExercicio();
        }

        // Preenche o campo Data da Compra Direta.
        $stJs .= "$('stDtCompraDireta').value = '".$stDtAutorizacao."';\n";
        $stJs .= "$('HdnDtCompraDireta').value = '".$stDtAutorizacao."';\n";
    break;
}

echo $stJs;
?>
