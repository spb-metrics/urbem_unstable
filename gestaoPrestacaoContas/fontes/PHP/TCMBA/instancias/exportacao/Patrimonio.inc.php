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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-08-21 11:36:17 -0300 (Qui, 21 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

    include_once( CAM_GPC_TCMBA_MAPEAMENTO."TTBAPatrimonio.class.php" );
    $obTMapeamento = new TTBAPatrimonio();
    $obTMapeamento->setDado('exercicio',Sessao::getExercicio());
    $obTMapeamento->setDado('cod_entidade',$stEntidades);
    $obTMapeamento->setDado('dt_inicio',$arFiltro['stDataInicial']);
    $obTMapeamento->setDado('dt_fim',$arFiltro['stDataFinal']);
    $obTMapeamento->recuperaDadosTribunal($rsPatrimonio);

    $inCount = 1;
    //Numero de linhas limite menos 2, para considerar o cabecalho e o rodape
    $inLimite = 4998;

    $arNewPatrimonio = array();
    $arRSPatrimonio = array();

    //Adiciona string 'patrimonio' na primeira linha do arquivo
    Sessao::write('titulo', 'patrimonio');

    if ($rsPatrimonio->getNumLinhas() > $inLimite) {
        foreach ($rsPatrimonio->arElementos as $arElementos) {
            $arNewPatrimonio[] = $arElementos;
            if (($inCount % $inLimite) == 0 ) {
                $rsNewPatrimonio = new RecordSet();
                $rsNewPatrimonio->preenche($arNewPatrimonio);
                $arRSPatrimonio[] = $rsNewPatrimonio;
                $arNewPatrimonio = array();
            }
            $inCount++;
        }
        if (count($arNewPatrimonio) > 0) {
            $rsNewPatrimonio = new RecordSet();
            $rsNewPatrimonio->preenche($arNewPatrimonio);
            $arRSPatrimonio[] = $rsNewPatrimonio;
        }
    } else {
        $arRSPatrimonio[] = $rsPatrimonio;
    }

    foreach ($arRSPatrimonio as $inKey => $rsNewPatrimonio) {

        $arArquivo = explode('.',$stArquivo);

        $obExportador->addArquivo($arArquivo[0] . ($inKey + 1) . '.' . $arArquivo[1]);
        $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

        $obExportador->roUltimoArquivo->addBloco($rsNewPatrimonio);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]1"); //Reservado
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]".$inCodUnidadeGestora); //unidade_gestora
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tombo_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("anterior_siga");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("funcionario_responsavel");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_aquisicao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_baixa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        unset($rsNewPatrimonio);
    }

/*
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
*/