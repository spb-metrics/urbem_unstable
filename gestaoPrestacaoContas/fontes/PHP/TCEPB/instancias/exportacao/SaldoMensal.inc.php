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

    * Data de Criação   : 17/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-06.03.00
*/

    include_once( CAM_GPC_TPB_MAPEAMENTO."TTPBSaldoMensal.class.php" );
    $obTMapeamento = new TTPBSaldoMensal();
    $obTMapeamento->setDado( 'exercicio'   , Sessao::getExercicio() );
    $obTMapeamento->setDado( 'inMes'       , $inMes );
    $obTMapeamento->setDado( 'stEntidades' , $stEntidades );
    $obTMapeamento->recuperaTodos( $arRecordSet[$stArquivo] );

    $obExportador->roUltimoArquivo->addBloco( $arRecordSet[$stArquivo] );
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna( "reservado_tse" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado( "NUMERICO_ZEROS_ESQ" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna( "conta_corrente" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 12 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna( "vl_extrato" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado( "VALOR_ZEROS_ESQ" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 16 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna( "reservado_tse" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado( "NUMERICO_ZEROS_ESQ" );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
