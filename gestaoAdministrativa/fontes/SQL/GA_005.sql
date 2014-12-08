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
* $Revision: 29583 $
* $Name$
* $Author: gris $
* $Date: 2008-03-27 14:37:38 -0300 (Qui, 27 Mar 2008) $
*
* Versão 005.
* GA 1.40.4
*/

ALTER TABLE licitacao.contrato_aditivos_anulacao ALTER COLUMN exercicio TYPE CHAR(4);

ALTER TABLE normas.norma ALTER COLUMN num_norma TYPE VARCHAR(30);

INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) 
     VALUES ( 1, 2,  1, 'Relatório de Organograma'                 , 'organograma.rptdesign');

INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) 
     VALUES ( 1, 2,  2, 'Relatório de Locais'                 , 'locais.rptdesign');

INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) 
     VALUES ( 1, 2,  3, 'Relatório de Auditoria'                 , 'auditoria.rptdesign');

INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) 
     VALUES ( 1, 2,  4, 'Relatório de Permissões'                 , 'permissao.rptdesign');
INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) 
     VALUES ( 1, 2,  5, 'Relatório de Usuário'                 , 'usuario.rptdesign');
