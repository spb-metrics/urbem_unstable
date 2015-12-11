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
* Versao 2.04.7
*
* Fabio Bertoldi - 20151210
*
*/

----------------
-- Ticket #20569
----------------

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 963
     , '2015'
     , 'Vlr. Ref. Estorno de Depreciação Acumulada mês'
     , TRUE
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 964
     , '2015'
     , 'Vlr. Ref. Lançamento Contábil de Depreciação por Baixa de Bem'
     , TRUE
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 965
     , '2015'
     , 'Vlr. Ref. Estorno Lançamento Contábil de Depreciação por Baixa de Bem'
     , TRUE
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 966
     , '2015'
     , 'Vlr. Ref. Lançamento Contábil de Baixa de Bem'
     , TRUE
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 967
     , '2015'
     , 'Vlr. Ref. Estorno de Lançamento Contábil de Baixa de Bem'
     , TRUE
     , TRUE
     );

CREATE TABLE contabilidade.lancamento_baixa_patrimonio_depreciacao(
    id              INTEGER         NOT NULL,
    timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    exercicio       CHAR(4)         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    tipo            CHAR(1)         NOT NULL,
    cod_lote        INTEGER         NOT NULL,
    sequencia       INTEGER         NOT NULL,
    cod_bem         INTEGER         NOT NULL,
    estorno         BOOLEAN         NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_lancamento_baixa_patrimonio_depreciacao   PRIMARY KEY (id),
    CONSTRAINT fk_lancamento_baixa_patrimonio_depreciacao_1 FOREIGN KEY                         (exercicio, cod_entidade, tipo, cod_lote, sequencia)
                                                            REFERENCES contabilidade.lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia),
    CONSTRAINT fk_lancamento_baixa_patrimonio_depreciacao_2 FOREIGN KEY                         (cod_bem)
                                                            REFERENCES patrimonio.bem           (cod_bem)
);
GRANT ALL ON contabilidade.lancamento_baixa_patrimonio_depreciacao TO urbem;

