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
    * Página de Include Oculta - ABERTURA DA LICITAÇÃO

    * Data de Criação   : 28/02/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore
    * $Id: ABL.inc.php 62359 2015-04-28 19:03:01Z carlos.silva $
    * $Rev: 62359 $
    * $Author: carlos.silva $
    * $Date: 2015-04-28 16:03:01 -0300 (Ter, 28 Abr 2015) $

*/

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAberturaLicitacao.class.php" );

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$obTTCMGOAberturaLicitacao = new TTCMGOAberturaLicitacao();
$obTTCMGOAberturaLicitacao->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
$obTTCMGOAberturaLicitacao->setDado('dtFim'      , $arFiltroRelatorio['stDataFinal']   );
$obTTCMGOAberturaLicitacao->setDado('inMes'      , $arFiltroRelatorio['inMes']         );
$obTTCMGOAberturaLicitacao->setDado('stExercicio', sessao::getExercicio()              );
$obTTCMGOAberturaLicitacao->setDado('stEntidades', $stEntidades                        );

$obTTCMGOAberturaLicitacao->recuperaExportacao10($rsRecordSetABL10);
$obTTCMGOAberturaLicitacao->recuperaExportacao11($rsRecordSetABL11);
$obTTCMGOAberturaLicitacao->recuperaExportacao12($rsRecordSetABL12);
$obTTCMGOAberturaLicitacao->recuperaExportacao13($rsRecordSetABL13);

$inCount = 0;

//10
if ( count($rsRecordSetABL10->getElementos()) > 0) {
    //$boChave = true;
    foreach ($rsRecordSetABL10->getElementos() as $arABL10) {
    
        $arABL10['nro_sequencial'] = ++$inCount;
        $stChave10 = $arABL10['cod_orgao'].$arABL10['cod_unidade'].$arABL10['exercicio_licitacao'].$arABL10['num_processo_licitatorio'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arABL10));

        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade_licitacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_modalidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_procedimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_abertura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_edital_convite");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_edital_publicacao_do");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_recebimento_doc");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_licitacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_objeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(500);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("regime_execucao_obras");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_convidado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("clausula_prorrogacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_medida_prazo_execucao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prazo_execucao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("criterio_aceitabilidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_tabela");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        
        $stChave11 = '';
        foreach ($rsRecordSetABL11->getElementos() as $arABL11) {
            //$inCount++;
            $stChave11 = $arABL11['cod_orgao'].$arABL11['cod_unidade'].$arABL11['exercicio_licitacao'].$arABL11['num_processo_licitatorio'].$arABL11['num_lote'].$arABL11['num_item'];                
            
            if ($stChave10 == $arABL11['cod_orgao'].$arABL11['cod_unidade'].$arABL11['exercicio_licitacao'].$arABL11['num_processo_licitatorio']){
                
                $arABL11['nro_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arABL11));

                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_cotacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_unitario");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alienacao_bem");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(699);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }
        }
        
        $stChave12 = '';
        foreach ($rsRecordSetABL12->getElementos() as $arABL12) {
            //$inCount++;
            //$stChave12 = $arABL12['cod_orgao'].$arABL12['cod_unidade'].$arABL12['exercicio_licitacao'].$arABL12['num_processo_licitatorio'].$arABL12['num_lote'].$arABL12['num_item'];
            $stChave12 = $arABL12['cod_orgao'].$arABL12['cod_unidade'].$arABL12['exercicio_licitacao'].$arABL12['num_processo_licitatorio'];                
            
            if ($stChave11 == $arABL12['cod_orgao'].$arABL12['cod_unidade'].$arABL12['exercicio_licitacao'].$arABL12['num_processo_licitatorio'].$arABL12['num_lote'].$arABL12['num_item']){
                
                $arABL12['nro_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arABL12));

                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(735);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                
            }
        }
        
        $stChave13 = '';
        foreach ($rsRecordSetABL13->getElementos() as $arABL13) {
            //$inCount++;
            //$stChave13 = $arABL13['cod_orgao'].$arABL13['cod_unidade'].$arABL13['exercicio_licitacao'].$arABL13['num_processo_licitatorio'].$arABL12['num_lote'].$arABL12['num_item'];                
            
            if ($stChave12 == $arABL13['cod_orgao'].$arABL13['cod_unidade'].$arABL13['exercicio_licitacao'].$arABL13['num_processo_licitatorio']){
                
                $arABL13['nro_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arABL13));

                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_proj_atividade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento_despesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(966);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }
        }
    }
}
    
//tipo99
$arTemp[0] = array( 'tipo_registro'=> 99, 'brancos'=> '', 'nro_sequencial' => $inCount++ );
      
$arRecordSet[$stArquivo] = new RecordSet();
$arRecordSet[$stArquivo]->preenche( $arTemp );
    
$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(389);
    
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

unset($rsRecordSetABL10);
unset($rsRecordSetABL11);
unset($rsRecordSetABL12);
unset($rsRecordSetABL13);
unset($rsRecordSetABL99);

?>