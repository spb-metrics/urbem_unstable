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
* Versao 2.04.1
*
* Fabio Bertoldi - 20150618
*
*/

----------------
-- Ticket #23021
----------------

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
     ( 3051
     , 364
     , 'FMConfigurarTipoDocumento.php'
     , 'configurar'
     , 60
     , ''
     , 'Configurar Tipo de Documento'
     , TRUE
     );

CREATE TABLE tcmgo.tipo_documento_tcm (
    cod_documento_tcm       INTEGER     NOT NULL,
    descricao               varchar(80) NOT NULL,
    CONSTRAINT pk_tipo_documento_tcm    PRIMARY KEY (cod_documento_tcm)
);
GRANT ALL ON tcmgo.tipo_documento_tcm TO urbem;

INSERT INTO tcmgo.tipo_documento_tcm VALUES (1, 'CPF'                                      );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (2, 'CNPJ'                                     );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (3, 'Documento de Estrangeiros'                );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (4, 'Certidão de Regularidade do INSS'         );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (5, 'Certidão de Regularidade do FGTS'         );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (6, 'Certidão Negativa de Débitos Trabalhistas');

CREATE TABLE tcmgo.documento_de_para (
    cod_documento_tcm       INTEGER     NOT NULL,
    cod_documento           INTEGER     NOT NULL,
    CONSTRAINT pk_documento_de_para     PRIMARY KEY                         (cod_documento_tcm, cod_documento),
    CONSTRAINT fk_documento_de_para_1   FOREIGN KEY                         (cod_documento_tcm)
                                        REFERENCES  tcmgo.tipo_documento_tcm(cod_documento_tcm),
    CONSTRAINT fk_documento_de_para_2   FOREIGN KEY                         (cod_documento)
                                        REFERENCES  licitacao.documento     (cod_documento)
);
GRANT ALL ON tcmgo.documento_de_para TO urbem;


---------------
-- Ticket #23018
----------------

CREATE TABLE tcemg.registro_precos_licitacao(
    cod_entidade                INTEGER         NOT NULL,
    numero_registro_precos      INTEGER         NOT NULL,
    exercicio                   CHAR(4)         NOT NULL,
    interno                     BOOLEAN         NOT NULL,
    numcgm_gerenciador          INTEGER         NOT NULL,

    cod_licitacao               INTEGER         NOT NULL,
    cod_modalidade              INTEGER         NOT NULL,
    cod_entidade_licitacao      INTEGER         NOT NULL,
    exercicio_licitacao         CHAR(4)         NOT NULL,

    CONSTRAINT pk_registro_precos_licitacao     PRIMARY KEY                       (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador, cod_licitacao, cod_modalidade, cod_entidade_licitacao, exercicio_licitacao),
    CONSTRAINT fk_registro_precos_licitacao_1   FOREIGN KEY                       (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador)
                                                REFERENCES  tcemg.registro_precos (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador),
    CONSTRAINT fk_registro_precos_licitacao_2   FOREIGN KEY                       (cod_licitacao, cod_modalidade, cod_entidade_licitacao, exercicio_licitacao)
                                                REFERENCES  licitacao.licitacao   (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
);
GRANT ALL ON tcemg.registro_precos_licitacao TO urbem;


----------------
-- Ticket #22969
----------------

UPDATE administracao.acao
   SET nom_acao  = 'Básicos'
     , parametro = 'basicos'
     , ordem     = 1
 WHERE cod_acao = 1851
     ;

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
     ( 3052
     , 382
     , 'FLManterExportacao.php'
     , 'programa'
     , 2
     , ''
     , 'Programa'
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
     ( 3053
     , 382
     , 'FLManterExportacao.php'
     , 'orcamento'
     , 3
     , ''
     , 'Orçamento'
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
     ( 3054
     , 382
     , 'FLManterExportacao.php'
     , 'ldo'
     , 4
     , ''
     , 'LDO'
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
     ( 3055
     , 382
     , 'FLManterExportacao.php'
     , 'programacao'
     , 5
     , ''
     , 'Programação Financeira'
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
     ( 3056
     , 382
     , 'FLManterExportacao.php'
     , 'consumo'
     , 6
     , ''
     , 'Consumo'
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
     ( 3057
     , 382
     , 'FLManterExportacao.php'
     , 'informes'
     , 7
     , ''
     , 'Informes Mensais'
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
     ( 3058
     , 382
     , 'FLManterExportacao.php'
     , 'consolidados'
     , 8
     , ''
     , 'Consolidados'
     , TRUE
     );


----------------
-- Ticket #22360
----------------

DROP FUNCTION tcmgo.arquivo_afr_exportacao10 (varchar, varchar, varchar, varchar, bpchar);
DROP FUNCTION tcmgo.arquivo_afr_exportacao11 (varchar, varchar, varchar, varchar, bpchar);


----------------
-- Ticket #23021
----------------

DELETE FROM administracao.configuracao_entidade WHERE cod_modulo = 42;

CREATE TABLE tcmgo.poder(
    cod_poder       INTEGER     NOT NULL,
    nom_poder       VARCHAR(20) NOT NULL,
    CONSTRAINT pk_poder         PRIMARY KEY (cod_poder)
);
GRANT ALL ON tcmgo.poder TO urbem;

INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (1, 'Poder Executivo'  );
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (2, 'Poder Legislativo');
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (3, 'RPPS'             );
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (4, 'Outros'           );

CREATE TABLE tcmgo.configuracao_orgao_unidade(
    exercicio       CHAR(4)     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    cod_poder       INTEGER     NOT NULL,
    num_orgao       INTEGER     NOT NULL,
    num_unidade     INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_orgao_unidade    PRIMARY KEY                   (exercicio, cod_entidade, cod_poder),
    CONSTRAINT fk_configuracao_orgao_unidade_1  FOREIGN KEY                   (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade (exercicio, cod_entidade),
    CONSTRAINT fk_configuracao_orgao_unidade_2  FOREIGN KEY                   (cod_poder)
                                                REFERENCES tcmgo.poder        (cod_poder),
    CONSTRAINT fk_configuracao_orgao_unidade_3  FOREIGN KEY                   (exercicio, num_orgao, num_unidade)
                                                REFERENCES orcamento.unidade  (exercicio, num_orgao, num_unidade)
);
GRANT ALL ON tcmgo.configuracao_orgao_unidade TO urbem;

