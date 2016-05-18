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
  * Página de Include Oculta - Exportação Arquivos TCEMG - RESPINF.csv
  * Data de Criação: 11/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Arthur Cruz
  *
  * @ignore
  * $Id: RESPINF.inc.php 65302 2016-05-11 11:35:18Z evandro $
  * $Date: 2016-05-11 08:35:18 -0300 (Qua, 11 Mai 2016) $
  * $Author: evandro $
  * $Rev: 65302 $
  *
*/
/**
* RESPINF.csv | Autor : Arthur Cruz
*/
require_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGRESPINF.class.php";
$rsRecordSet = new RecordSet();
$obTTCEMGRESPINF = new TTCEMGRESPINF();
$obTTCEMGRESPINF->setDado('entidades', $stEntidades);
$obTTCEMGRESPINF->setDado('dt_inicial', $arDatasInicialFinal["stDtInicial"]);
$obTTCEMGRESPINF->setDado('dt_final', $arDatasInicialFinal["stDtFinal"]);
$obTTCEMGRESPINF->recuperaDados($rsRecordSet);

if ( $rsRecordSet->getNumLinhas() > 0 ) {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSet);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
}