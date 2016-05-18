
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
* Versao 2.05.1
*
* Fabio Bertoldi - 20160516
*
*/

----------------
-- Ticket #23647
----------------

CREATE TABLE tcmba.tipo_conciliacao_lancamento_contabil (
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    descricao               CHAR(100)   NOT NULL,
    CONSTRAINT pk_tipo_conciliacao_lancamento_contabil PRIMARY KEY (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.tipo_conciliacao_lancamento_contabil TO urbem;

INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (1, 'Cheque não compensado - Banco'        );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (2, 'Débito não lançado - Contábil'        );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (4, 'Débito indevido pelo banco'           );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (5, 'Tarifa cobrada não lançada - Contábil');
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (6, 'Tarifa cobrada indevida banco'        );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (7, 'Depósito não lançado pelo banco'      );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (8, 'Crédito não lançado - Contábil'       );
INSERT INTO tcmba.tipo_conciliacao_lancamento_contabil VALUES (9, 'Crédito indevido pelo banco'          );
 

CREATE TABLE tcmba.conciliacao_lancamento_contabil (
    cod_plano               INTEGER     NOT NULL,
    exercicio_conciliacao   CHAR(4)     NOT NULL,
    mes                     INTEGER     NOT NULL,
    cod_lote                INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    sequencia               INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    tipo_valor              CHAR(1)     NOT NULL,
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    CONSTRAINT pk_conciliacao_lancamento_contabil   PRIMARY KEY (cod_plano, exercicio_conciliacao, mes, cod_lote, exercicio, tipo, sequencia, cod_entidade, tipo_valor, cod_tipo_conciliacao),
    CONSTRAINT fk_conciliacao_lancamento_contabil_1 FOREIGN KEY (cod_tipo_conciliacao)
                                                    REFERENCES tcmba.tipo_conciliacao_lancamento_contabil (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.conciliacao_lancamento_contabil TO urbem;

