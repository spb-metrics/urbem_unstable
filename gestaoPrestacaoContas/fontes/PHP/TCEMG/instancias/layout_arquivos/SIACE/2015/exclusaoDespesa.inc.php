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
    * Arquivo de geracao do arquivo exclusaoDespesa TCM/MG
    * Data de Criação   : 23/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: exclusaoDespesa.inc.php 62522 2015-05-18 14:22:51Z evandro $
    */

    $arFiltros = Sessao::read('filtroRelatorio');
    $arArquivo = array(0 => array( 'mes'                        => $arFiltros['inPeriodo'],
                                   'fundacoes_transf_corrente'  => '000',
                                   'autarquias_transf_corrente' => '000',
                                   'emprestdep_transf_corrente' => '000',
                                   'demaisent_transf_corrente'  => '000',
                                   'fundacoes_transf_capital'   => '000',
                                   'autarquias_transf_capital'  => '000',
                                   'emprestdep_transf_capital'  => '000',
                                   'demaisent_transf_capital'   => '000',
                                   'cod_tipo'                   => '04',
                                 ),
                       1 => array( 'mes'                        => $arFiltros['inPeriodo'],
                                   'fundacoes_transf_corrente'  => '000',
                                   'autarquias_transf_corrente' => '000',
                                   'emprestdep_transf_corrente' => '000',
                                   'demaisent_transf_corrente'  => '000',
                                   'fundacoes_transf_capital'   => '000',
                                   'autarquias_transf_capital'  => '000',
                                   'emprestdep_transf_capital'  => '000',
                                   'demaisent_transf_capital'   => '000',
                                   'cod_tipo'                   => '05',
                                 ),
                      );

    $rsArquivo = new RecordSet();
    $rsArquivo->preenche($arArquivo);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('autarquias_transf_capital');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('demaisent_transf_capital');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('emprestdep_transf_corrente');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('fundacoes_transf_corrente');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('autarquias_transf_corrente');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('demaisent_transf_corrente');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('emprestdep_transf_capital');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('fundacoes_transf_capital');
    
    
    

    
