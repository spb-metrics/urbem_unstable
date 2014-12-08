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
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-27 14:37:38 -0300 (Qui, 27 Mar 2008) $
*
* Versão 001.
*/

--
--  Ticket #385 - Inserindo link relatorio Protocolo de Processo
--
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  4, 'Recibo de Processo'                 , 'reciboProcesso.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  5, 'Relatório de Despacho de Processo'  , 'despacho.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  6, 'Recibo de Entrega de Processos'     , 'imprimeReciboEntrega.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  7, 'Arquivamento de Processo Temporário', 'arquivaProcessoTemporario.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  8, 'Arquivamento de Processo Definitivo', 'arquivaProcessoDefinitivo.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5,  9, 'Relatório de Assuntos Analítico'    , 'assuntoAnalitico.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5, 10, 'Relatório de Assuntos Sintético'    , 'assuntoSintetico.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1,  5, 11, 'Relatório de Processo'              , 'relatorioProcesso.rptdesign');
  INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES ( 1, 19,  1, 'Relatório de Nível'                 , 'relatorioNivel.rptdesign');


--
--
--
--    DELETE FROM administracao.funcao_externa WHERE corpo_pl IS NULL;
