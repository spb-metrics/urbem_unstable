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
* $Id:  $
*
* Versão 1.98.7
*/

----------------
-- Ticket #16437
----------------

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN (684,1523,1524,1525,1526,1527,1528,1529,1531,1532,1533)
     ;

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (684,1523,1524,1525,1526,1527,1528,1529,1531,1532,1533)
     ;

DELETE
  FROM administracao.acao
 WHERE cod_acao IN (684,1523,1524,1525,1526,1527,1528,1529,1531,1532,1533)
     ;

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 318
     ;

