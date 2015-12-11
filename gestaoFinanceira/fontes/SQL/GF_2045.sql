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
*
* Script de DDL e DML
*
* Versao 2.04.5
*
* Fabio Bertoldi - 20151111
*
*/

----------------
-- Ticket #23376
----------------

ALTER TABLE empenho.empenho_contrato ADD COLUMN   exercicio_contrato CHAR(4);
UPDATE      empenho.empenho_contrato SET          exercicio_contrato = exercicio;
ALTER TABLE empenho.empenho_contrato ALTER COLUMN exercicio_contrato SET NOT NULL;

ALTER TABLE empenho.empenho_contrato DROP CONSTRAINT fk_empenho_contrato_2;

ALTER TABLE empenho.empenho_contrato ADD CONSTRAINT fk_empenho_contrato_2 FOREIGN KEY                   (exercicio_contrato, cod_entidade, num_contrato)
                                                                          REFERENCES licitacao.contrato (exercicio         , cod_entidade, num_contrato);
