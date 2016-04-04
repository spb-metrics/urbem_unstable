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
* Versao 2.04.8
*
* Fabio Bertoldi - 20160316
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
SELECT 962
     , '2015'
     , 'Vlr. Ref. Depreciação Acumulada mês'
     , TRUE
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM contabilidade.historico_contabil
              WHERE cod_historico = 962
                AND exercicio     = '2015'
           )
     ;

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
SELECT 962
     , '2016'
     , 'Vlr. Ref. Depreciação Acumulada mês'
     , TRUE
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM contabilidade.historico_contabil
              WHERE cod_historico = 962
                AND exercicio     = '2016'
           )
     ;


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
     , '2016'
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
     , '2016'
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
     , '2016'
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
     , '2016'
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
     , '2016'
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


----------------
-- Ticket #23482
----------------

CREATE TABLE licitacao.contrato_apostila(
    cod_apostila        INTEGER         NOT NULL,
    num_contrato        INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    cod_alteracao       INTEGER         NOT NULL,
    descricao           VARCHAR(250)    NOT NULL,
    data_apostila       DATE            NOT NULL,
    valor_apostila      NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_contrato_apostila     PRIMARY KEY     (cod_apostila, num_contrato, cod_entidade, exercicio),
    CONSTRAINT fk_contrato_apostila_1   FOREIGN KEY                   (num_contrato, cod_entidade, exercicio)
                                        REFERENCES licitacao.contrato (num_contrato, cod_entidade, exercicio)
);
GRANT ALL ON licitacao.contrato_apostila TO urbem;


UPDATE administracao.acao SET ordem = 11 WHERE cod_acao = 3087;


INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3105
     , 342
     , 'FLManterApostilaContrato.php' 
     , 'incluir'
     , 15
     , ''
     , 'Incluir Apostila de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3106
     , 342
     , 'FLManterApostilaContrato.php'
     , 'alterar'
     , 16
     , ''
     , 'Alterar Apostila de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3107
     , 342
     , 'FLManterApostilaContrato.php'
     , 'excluir'
     , 17
     , ''
     , 'Excluir Apostila de Contrato'
     , TRUE
     );


INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3108
     , 428
     , 'FLManterApostilaContrato.php' 
     , 'incluir'
     , 9
     , ''
     , 'Incluir Apostila de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3109
     , 428
     , 'FLManterApostilaContrato.php'
     , 'alterar'
     , 10
     , ''
     , 'Alterar Apostila de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3110
     , 428
     , 'FLManterApostilaContrato.php'
     , 'excluir'
     , 11
     , ''
     , 'Excluir Apostila de Contrato'
     , TRUE
     );


----------------
-- Ticket #22396
----------------

CREATE TYPE participante_documentos AS (
    cod_licitacao       INTEGER,
    cod_documento       INTEGER,
    dt_validade         DATE,
    cgm_fornecedor      INTEGER,
    cod_modalidade      INTEGER,
    cod_entidade        INTEGER,
    exercicio           CHAR,
    num_documento       VARCHAR,
    dt_emissao          DATE,
    timestamp           TIMESTAMP
 );
 
