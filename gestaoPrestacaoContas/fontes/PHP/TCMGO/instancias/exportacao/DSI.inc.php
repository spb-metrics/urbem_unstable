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

include_once( CAM_GPC_TGO_MAPEAMENTO."TTGODSI.class.php" );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    
    $obTMapeamento = new TTGODSI();
    
    $obTMapeamento->setDado('exercicio' , Sessao::getExercicio());
    $obTMapeamento->setDado('dtInicio'  , $arFiltroRelatorio['stDataInicial']);
    $obTMapeamento->setDado('dtFim'     , $arFiltroRelatorio['stDataFinal']);
    $obTMapeamento->setDado('entidades' , $stEntidades);
    
    $obTMapeamento->recuperaDetalhamento10($rsDetalhamento);
    $obTMapeamento->recuperaDetalhamento11($rsResponsaveis);
    $obTMapeamento->recuperaDetalhamento12($rsPesquisa);
    $obTMapeamento->recuperaDetalhamento13($rsRecurso);
    $obTMapeamento->recuperaDetalhamento14($rsFornecedor);
    $obTMapeamento->recuperaDetalhamento15($rsCredenciado);
    
    $inCount = 0;
    
    // Registro 10 - Detalhamento da Dispensa ou da Inexigibilidade

    foreach ($rsDetalhamento->arElementos as $stChave) {
        $stChave['numero_sequencial'] = ++$inCount;
        $stKey = $stChave['cod_orgao'] . $stChave['cod_unidade'] . $stChave['num_processo'] . $stChave['ano_exercicio_processo'] . $stChave['tipo_processo'];
        
        $rsBloco = 'rsBloco_' . $inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($stChave));
        
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_abertura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_objeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("razao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao_termo_ratificacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculo_publicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
        
        // Registro 11 - Detalhamento dos Responsáveis pela dispensa ou inexigibilidade
        
        foreach ($rsResponsaveis->arElementos as $stChaveResponsaveis) {
            $stKey11 = $stChaveResponsaveis['cod_orgao'] . $stChaveResponsaveis['cod_unidade'] . $stChaveResponsaveis['num_processo'] . $stChaveResponsaveis['ano_exercicio_processo'] . $stChaveResponsaveis['tipo_processo']; //. $stChaveResponsaveis['tipo_resp'] . $stChaveResponsaveis['num_cpf_responsavel'];
            
            if ($stKey11 === $stKey) {
                $stChaveResponsaveis['numero_sequencial'] = ++$inCount;
                
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChaveResponsaveis));
                
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_resp");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cpf_responsavel");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_responsavel");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("setor");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("telefone");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("e_mail");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(715);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
        
        // Registro 12 - Detalhamento da Pesquisa de Preços / Orçamentos em planilha / Referência de preços do credenciamento / chamada pública
        
        foreach ($rsPesquisa->arElementos as $stChavePesquisa) {
            $stKey12 = $stChavePesquisa['cod_orgao'] . $stChavePesquisa['cod_unidade'] . $stChavePesquisa['num_processo'] . $stChavePesquisa['ano_exercicio_processo'] . $stChavePesquisa['tipo_processo']; //. $stChavePesquisa['num_lote'] . $stChavePesquisa['num_item'];
            
            if ($stKey12 === $stKey) {
                $stChavePesquisa['numero_sequencial'] = ++$inCount;
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChavePesquisa));
                
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cot_precos_unitario");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(746);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
        
        // Registro 13 - Detalhamento dos Recursos Orçamentários
        
        foreach ($rsRecurso->arElementos as $stChaveRecurso) {
            $stKey13 = $stChaveRecurso['cod_orgao'] . $stChaveRecurso['cod_unidade'] . $stChaveRecurso['num_processo'] . $stChaveRecurso['ano_exercicio_processo'] . $stChaveRecurso['tipo_processo']; //. $stChaveRecurso['cod_funcao'] . $stChaveRecurso['cod_subfuncao'] . $stChaveRecurso['cod_programa'] . $stChaveRecurso['natureza_acao'] . $stChaveRecurso['num_proj_ativ'] . $stChaveRecurso['elemento_despesa'] . $stChaveRecurso['subelemento'] . $stChaveRecurso['cod_fonte_recurso'];
            
            if ($stKey13 === $stKey) {
                $stChaveRecurso['numero_sequencial'] = ++$inCount;
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChaveRecurso));
                
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_acao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_proj_ativ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elemento_despesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(978);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
        
        // Registro 14 - Detalhamento do Fornecedor contratado por dispensa ou inexigibilidade
        
        foreach ($rsFornecedor->arElementos as $stChaveFornecedor) {
            $stKey14 = $stChaveFornecedor['cod_orgao'] . $stChaveFornecedor['cod_unidade'] . $stChaveFornecedor['num_processo'] . $stChaveFornecedor['ano_exercicio_processo'] . $stChaveFornecedor['tipo_processo']; //. $stChaveFornecedor['tipo_documento'] . $stChaveFornecedor['num_documento'] . $stChaveFornecedor['num_lote'] . $stChaveFornecedor['num_item'];
            
            if ($stKey14 === $stKey) {
                $stChaveFornecedor['numero_sequencial'] = ++$inCount;
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChaveFornecedor));
                
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_razao_social");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(758);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
        
        //Registro 15 - Detalhamento do Credenciado Preencher este detalhamento somente para processos de inexigibilidade por credenciamento / chamada pública ou dispensa por chamada pública.
        foreach ($rsCredenciado->arElementos as $stChaveCredenciado) {
            $stKey15 = $stChaveCredenciado['cod_orgao'] . $stChaveCredenciado['cod_unidade'] . $stChaveCredenciado['num_processo'] . $stChaveCredenciado['ano_exercicio_processo'] . $stChaveCredenciado['tipo_processo']; // . $stChaveCredenciado['tipo_documento'] . $stChaveCredenciado['num_documento'] . $stChaveCredenciado['dt_credenciamento'] . $stChaveCredenciado['num_lote'] . $stChaveCredenciado['num_item'];
            
            if ($stKey15 === $stKey && ( $stChave['tipo_processo'] == 3 OR $stChave['tipo_processo'] == 4 ) ) {
                $stChaveCredenciado['numero_sequencial'] = ++$inCount;
                $rsBloco = 'rsBloco_' . $inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($stChaveCredenciado));
                
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_exercicio_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_credenciamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_razao_social");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(776);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
    }

    $arTemp[0] = array('tipo_registro'=> '99', 'brancos'=> '', 'numero_sequencial' => ++$inCount);

    $arFinalizador = new RecordSet();
    $arFinalizador->preenche($arTemp);

    $obExportador->roUltimoArquivo->addBloco($arFinalizador);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1030);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);