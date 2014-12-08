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
   /*
    * Arquivo de geracao do arquivo despesaTotalPessoalPL.txt
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO . 'FTCEMGDespesaTotalPessoalPL.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    if ($arFiltros['inPeriodo']<10) {
        $stMes = "0".$arFiltros['inPeriodo'];
    } else {
        $stMes = $arFiltros['inPeriodo'];
    }

    $dtPeriodoInicial = "01/".$stMes."/".Sessao::read('exercicio');
    $dtPeriodoFinal   = date("t",mktime(0,0,0,$arFiltros['inPeriodo'],1,Sessao::read('exercicio')))."/".$stMes."/".Sessao::read('exercicio');

    $obFTCEMGDespesaTotalPessoalPL = new FTCEMGDespesaTotalPessoalPL();
    $obFTCEMGDespesaTotalPessoalPL->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGDespesaTotalPessoalPL->setDado('dt_inicial'  , $dtPeriodoInicial);
    $obFTCEMGDespesaTotalPessoalPL->setDado('dt_final'    , $dtPeriodoFinal);

    //1 MES
    $arTemp[0]["mes"] = $arFiltros['inPeriodo'];

    //2 VENCIMENTOS E VANTAGENS FIXAS - SERVIDORES
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0%\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.01%\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.03%\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.09%\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.11.74.00.00.00\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.04.15.00.00.00\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.13.00.00.00.00\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.07.03.00.00.00\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.91%\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.94.01.01.00.00\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.94.01.02.00.00\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.92%\'
               and conta_despesa.cod_estrutural != \'3.1.9.0.16.04.00.00.00\'
               and conta_despesa.cod_estrutural not like \'3.1.9.0.34%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuVencimentosVantagens"] = $rsTemp->getCampo("valor");

    //3 APOSENTADORIAS E REFORMAS
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.01%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuAposentadorias"] = $rsTemp->getCampo("valor");

    //4 PENSOES
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.03%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuPensoes"] = $rsTemp->getCampo("valor");

    //5 SALARIO-FAMILIA
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.09%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSalarioFamilia"] = $rsTemp->getCampo("valor");

    //6 SUBSIDIOS
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.11.74.00.00.00\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSubsidios"] = $rsTemp->getCampo("valor");

    //7 OBRIGAÇÕES PATRONAIS
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.04.15.00.00.00\'
                OR conta_despesa.cod_estrutural = \'3.1.9.0.13.00.00.00.00\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuObrigacoesPatronais"] = $rsTemp->getCampo("valor");

    //8 REPASSE PATRONAL
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.07.03.00.00.00\'
                OR conta_despesa.cod_estrutural like \'3.1.9.1.13%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuRepassePatronal"] = $rsTemp->getCampo("valor");

    //9 SENTENCAS JUDICIAIS
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.91%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSentecasJudiciais"] = $rsTemp->getCampo("valor");

    //10 INDENIZACAO PARA DEMISSAO DE SERVIDORES/ EMPREGADOS
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.94.01.01.00.00\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuIndenizacaoDemissao"] = $rsTemp->getCampo("valor");

    //11 DESPESAS RELATIVAS A PROGRAMAS DE  DESLIGAMENTO VOLUNTARIO
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.94.01.02.00.00\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuDesligamentoVoluntario"] = $rsTemp->getCampo("valor");

    //12 DESPESAS DE EXERCICIOS ANTERIORES
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.92%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuDespesasAnteriores"] = $rsTemp->getCampo("valor");

    //13 INATIVOS E PENSIONISTAS CUSTEIO PRÓPRIO
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.01%\')
               and despesa.cod_recurso = 1";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] = $rsTemp->getCampo("valor");

    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.03%\')
               and despesa.cod_recurso = 1";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] += $rsTemp->getCampo("valor");
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] = number_format($arTemp[0]["nuInativosPensionistasCusteioProprio"],2,'.','');

    //14 CONVOCACAO EXTRAORDINARIA
    $stFiltro = " (conta_despesa.cod_estrutural = \'3.1.9.0.16.04.00.00.00\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuConvocacaoExtraordinaria"] = $rsTemp->getCampo("valor");

    //15 OUTRAS DESPESAS DE PESSOAL
    $stFiltro = " (conta_despesa.cod_estrutural like \'3.1.9.0.34%\')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuOutrasDespesas"] = $rsTemp->getCampo("valor");

    //16 Indica se não há nada a declarar referente a Outras Despesas de Pessoal
    if ($rsTemp->getCampo("valor") > 0) {
        $arTemp[0]["nadaDeclararPessoal"] = 'S';
    } else {
        $arTemp[0]["nadaDeclararPessoal"] = 'N';
    }

    $rsArquivo = new recordset();
    $rsArquivo->preenche($arTemp);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuVencimentosVantagens');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuAposentadorias');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuPensoes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSalarioFamilia');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSubsidios');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuObrigacoesPatronais');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuRepassePatronal');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSentecasJudiciais');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuIndenizacaoDemissao');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuDesligamentoVoluntario');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuDespesasAnteriores');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuInativosPensionistasCusteioProprio');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuConvocacaoExtraordinaria');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuOutrasDespesas');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nadaDeclararPessoal');
