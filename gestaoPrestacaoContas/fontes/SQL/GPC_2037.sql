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
* Versao 2.03.7
*
* Gelson W. Gonçalves - 20150210
*
*/
----------------
-- Ticket #22674
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 3042
          , 403
          , 'FMManterVinculoTipoVeiculo.php'
          , 'manter'
          , 50
          , ''
          , 'Configurar Tipo Veículo'
          );

CREATE TABLE tcern.especie_veiculo_tce (
    cod_especie_tce     INTEGER         NOT NULL,
    nom_especie_tce     VARCHAR(20)     NOT NULL,
    CONSTRAINT pk_especie_veiculo_tce   PRIMARY KEY     (cod_especie_tce)
);

GRANT ALL ON tcern.especie_veiculo_tce TO GROUP urbem;

INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (1, 'Passageiro');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (2, 'Carga');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (3, 'Misto');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (4, 'Corrida');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (5, 'Tração');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (6, 'Especial');
INSERT INTO tcern.especie_veiculo_tce(cod_especie_tce, nom_especie_tce) VALUES (7, 'Coleção');


CREATE TABLE tcern.tipo_veiculo_tce (
    cod_tipo_tce        INTEGER NOT NULL,
    nom_tipo_tce        VARCHAR(20),
    CONSTRAINT pk_tipo_veiculo_tce   PRIMARY KEY (cod_tipo_tce)
);

GRANT ALL ON tcern.tipo_veiculo_tce TO GROUP urbem;

INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (1,'BICICLETA');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (2,'CICLOMOTOR');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (3,'MOTONETA');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (4,'MOTOCICLETA');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (5,'TRICICLO');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (6,'AUTOMÓVEL');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (7,'MICRO ÔNIBUS');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (8,'ONIBUS');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (9,'BONDE');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (10,'REBOQUE');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (11,'SEMI-REBOQUE');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (12,'CHARRETE');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (13,'CAMIONETA');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (14,'CAMINHÃO');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (15,'CARROCA');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (16,'CARRO DE MÃO');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (17,'CAMINHÃO TRATOR');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (18,'TRATOR DE RODAS');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (19,'TRATOR ESTEIRAS');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (20,'TRATOR MISTO');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (21,'QUADRICICLO');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (22,'CAMINHONETE');
INSERT INTO tcern.tipo_veiculo_tce(cod_tipo_tce,nom_tipo_tce) VALUES (23,'EXPERIÊNCIA');

CREATE TABLE tcern.tipo_veiculo_vinculo (
    cod_tipo            INTEGER             NOT NULL,
    cod_especie_tce     INTEGER             NOT NULL,
    cod_tipo_tce        INTEGER             NOT NULL,
    CONSTRAINT pk_tipo_veiculo_vinculo      PRIMARY KEY                             (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_1    FOREIGN KEY                             (cod_tipo)
                                            REFERENCES frota.tipo_veiculo           (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_2    FOREIGN KEY                             (cod_especie_tce)
                                            REFERENCES tcern.especie_veiculo_tce    (cod_especie_tce),
    CONSTRAINT fk_tipo_veiculo_vinculo_3    FOREIGN KEY                             (cod_tipo_tce)
                                            REFERENCES tcern.tipo_veiculo_tce       (cod_tipo_tce)
);

GRANT ALL ON tcern.tipo_veiculo_vinculo TO GROUP urbem;

----------------
-- Ticket #22675
----------------
CREATE TABLE tcern.categoria_veiculo_tce (
    cod_categoria       INTEGER NOT NULL,
    nom_categoria       VARCHAR(20),
    CONSTRAINT pk_categoria_veiculo_tce  PRIMARY KEY (cod_categoria)
);

GRANT ALL ON tcern.categoria_veiculo_tce TO GROUP urbem;

INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (1,'Particular');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (2,'Aluguel');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (3,'Oficial');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (4,'Experiência');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (5,'Aprendizagem');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (6,'Fabricante');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (7,'Diplomático');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (8,'Consular');
INSERT INTO tcern.categoria_veiculo_tce (cod_categoria,nom_categoria) VALUES (9,'Origem Interna');

CREATE TABLE tcern.veiculo_categoria_vinculo (
    cod_veiculo       INTEGER NOT NULL,
    cod_categoria     INTEGER NOT NULL,
    CONSTRAINT pk_veiculo_categoria_veiculo    PRIMARY KEY (cod_veiculo),
    CONSTRAINT fk_veiculo_categoria_vinculo_1  FOREIGN KEY                            (cod_veiculo)
                                               REFERENCES frota.veiculo               (cod_veiculo),
    CONSTRAINT fk_veiculo_categoria_vinculo_2  FOREIGN KEY                            (cod_categoria)
                                               REFERENCES tcern.categoria_veiculo_tce (cod_categoria)
);

GRANT ALL ON tcern.veiculo_categoria_vinculo TO GROUP urbem;


----------------
-- Ticket #21253
----------------

ALTER TABLE tcemg.processo_adesao_registro_precos RENAME TO registro_precos;


ALTER TABLE tcemg.empenho_registro_precos DROP CONSTRAINT pk_empenho_registro_de_precos;
ALTER TABLE tcemg.empenho_registro_precos DROP CONSTRAINT fk_empenho_registro_de_precos_1;
ALTER TABLE tcemg.item_registro_precos    DROP CONSTRAINT pk_item_registro_precos; 
ALTER TABLE tcemg.item_registro_precos    DROP CONSTRAINT fk_item_registro_precos_1;
ALTER TABLE tcemg.lote_registro_precos    DROP CONSTRAINT fk_lote_registro_precos_1;
ALTER TABLE tcemg.lote_registro_precos    DROP CONSTRAINT pk_lote_registro_precos;
ALTER TABLE tcemg.lote_registro_precos    DROP CONSTRAINT uk_lote_registro_precos_1;
ALTER TABLE tcemg.registro_precos         DROP CONSTRAINT pk_processo_adesao_registro_precos;
ALTER TABLE tcemg.registro_precos         DROP CONSTRAINT fk_processo_adesao_registro_precos_1;
ALTER TABLE tcemg.registro_precos         DROP CONSTRAINT fk_processo_adesao_registro_precos_2;
ALTER TABLE tcemg.registro_precos         DROP CONSTRAINT fk_processo_adesao_registro_precos_3;


ALTER TABLE tcemg.registro_precos      RENAME COLUMN numero_processo_adesao         TO numero_registro_precos;
ALTER TABLE tcemg.registro_precos      RENAME COLUMN data_abertura_processo_adesao  TO data_abertura_registro_precos;
ALTER TABLE tcemg.registro_precos      RENAME COLUMN exercicio                      TO exercicio_unidade;
ALTER TABLE tcemg.registro_precos      RENAME COLUMN exercicio_adesao               TO exercicio;
ALTER TABLE tcemg.registro_precos      RENAME COLUMN objeto_adesao                  TO objeto;
ALTER TABLE tcemg.registro_precos      DROP   COLUMN natureza_procedimento;
ALTER TABLE tcemg.registro_precos      DROP   COLUMN data_publicacao_aviso_intencao;
ALTER TABLE tcemg.registro_precos      DROP   COLUMN exercicio_unidade;
ALTER TABLE tcemg.registro_precos      DROP   COLUMN num_unidade;
ALTER TABLE tcemg.registro_precos      DROP   COLUMN num_orgao;
ALTER TABLE tcemg.registro_precos      ADD    COLUMN interno BOOLEAN NOT NULL DEFAULT TRUE;

ALTER TABLE tcemg.lote_registro_precos RENAME COLUMN numero_processo_adesao TO numero_registro_precos;
ALTER TABLE tcemg.lote_registro_precos RENAME COLUMN exercicio_adesao       TO exercicio;
ALTER TABLE tcemg.lote_registro_precos ADD    COLUMN interno BOOLEAN;
UPDATE      tcemg.lote_registro_precos SET           interno = TRUE;
ALTER TABLE tcemg.lote_registro_precos ALTER  COLUMN interno SET NOT NULL;

ALTER TABLE tcemg.item_registro_precos RENAME COLUMN numero_processo_adesao TO numero_registro_precos;
ALTER TABLE tcemg.item_registro_precos RENAME COLUMN exercicio_adesao       TO exercicio;
ALTER TABLE tcemg.item_registro_precos RENAME COLUMN cgm_vencedor           TO cgm_fornecedor;
ALTER TABLE tcemg.item_registro_precos ADD    COLUMN interno BOOLEAN;
UPDATE      tcemg.item_registro_precos SET           interno = TRUE;
ALTER TABLE tcemg.item_registro_precos ALTER  COLUMN interno SET NOT NULL;
ALTER TABLE tcemg.item_registro_precos ADD    COLUMN ordem_classificacao_fornecedor INTEGER;
UPDATE      tcemg.item_registro_precos SET           ordem_classificacao_fornecedor = 1;
ALTER TABLE tcemg.item_registro_precos ALTER  COLUMN ordem_classificacao_fornecedor SET NOT NULL;

ALTER TABLE tcemg.empenho_registro_precos RENAME COLUMN numero_processo_adesao TO numero_registro_precos;
ALTER TABLE tcemg.empenho_registro_precos RENAME COLUMN exercicio_adesao       TO exercicio;
ALTER TABLE tcemg.empenho_registro_precos ADD    COLUMN interno BOOLEAN;
UPDATE      tcemg.empenho_registro_precos SET           interno = TRUE;
ALTER TABLE tcemg.empenho_registro_precos ALTER  COLUMN interno SET NOT NULL;
ALTER TABLE tcemg.empenho_registro_precos ADD    COLUMN cod_entidade_empenho INTEGER;
UPDATE      tcemg.empenho_registro_precos SET           cod_entidade_empenho = cod_entidade;
ALTER TABLE tcemg.empenho_registro_precos ALTER  COLUMN cod_entidade_empenho SET NOT NULL;


ALTER TABLE tcemg.registro_precos         ADD  CONSTRAINT pk_registro_precos             PRIMARY KEY                             (cod_entidade, numero_registro_precos, exercicio, interno);
ALTER TABLE tcemg.registro_precos         ADD  CONSTRAINT fk_registro_precos_1           FOREIGN KEY                             (numcgm)
                                                                                         REFERENCES sw_cgm                       (numcgm);
ALTER TABLE tcemg.registro_precos         ADD  CONSTRAINT fk_registro_precos_2           FOREIGN KEY                             (cgm_responsavel)
                                                                                         REFERENCES sw_cgm_pessoa_fisica         (numcgm);
ALTER TABLE tcemg.lote_registro_precos    ADD  CONSTRAINT fk_lote_registro_precos_1      FOREIGN KEY                             (cod_entidade, numero_registro_precos, exercicio, interno)
                                                                                         REFERENCES tcemg.registro_precos        (cod_entidade, numero_registro_precos, exercicio, interno);
ALTER TABLE tcemg.lote_registro_precos    ADD  CONSTRAINT pk_lote_registro_precos        PRIMARY KEY                             (cod_entidade, numero_registro_precos, exercicio, interno, cod_lote);
ALTER TABLE tcemg.lote_registro_precos    ADD  CONSTRAINT uk_lote_registro_precos_1      UNIQUE                                  (cod_entidade, numero_registro_precos, exercicio, interno, descricao_lote);
ALTER TABLE tcemg.item_registro_precos    ADD  CONSTRAINT pk_item_registro_precos        PRIMARY KEY                             (cod_entidade, numero_registro_precos, exercicio, interno, cod_lote, cod_item, cgm_fornecedor);
ALTER TABLE tcemg.item_registro_precos    ADD  CONSTRAINT fk_item_registro_precos_1      FOREIGN KEY                             (cod_entidade, numero_registro_precos, exercicio, interno, cod_lote)
                                                                                         REFERENCES tcemg.lote_registro_precos   (cod_entidade, numero_registro_precos, exercicio, interno, cod_lote);
ALTER TABLE tcemg.empenho_registro_precos ADD CONSTRAINT pk_empenho_registro_de_precos   PRIMARY KEY                             (cod_entidade, numero_registro_precos, exercicio, interno, cod_entidade_empenho, cod_empenho, exercicio_empenho);
ALTER TABLE tcemg.empenho_registro_precos ADD CONSTRAINT fk_empenho_registro_de_precos_2 FOREIGN KEY                             (cod_entidade, numero_registro_precos, exercicio, interno)
                                                                                         REFERENCES tcemg.registro_precos        (cod_entidade, numero_registro_precos, exercicio, interno);          
ALTER TABLE tcemg.empenho_registro_precos ADD CONSTRAINT fk_empenho_registro_de_precos_1 FOREIGN KEY                             (cod_entidade_empenho, cod_empenho, exercicio_empenho)
                                                                                         REFERENCES empenho.empenho              (cod_entidade, cod_empenho, exercicio);


CREATE TABLE tcemg.registro_precos_orgao(
    cod_entidade                    INTEGER         NOT NULL,
    numero_registro_precos          INTEGER         NOT NULL,
    exercicio_registro_precos       CHAR(4)         NOT NULL,
    interno                         BOOLEAN         NOT NULL,
    exercicio_unidade               CHAR(4)         NOT NULL,
    num_unidade                     INTEGER         NOT NULL,
    num_orgao                       INTEGER         NOT NULL,
    participante                    BOOLEAN         NOT NULL DEFAULT TRUE,
    numero_processo_adesao          INTEGER                 ,
    exercicio_adesao                CHAR(4)                 ,
    dt_publicacao_aviso_intencao    DATE                    ,
    dt_adesao                       DATE                    ,
    gerenciador                     BOOLEAN         NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_registro_precos_orgao             PRIMARY KEY                      (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, exercicio_unidade, num_unidade, num_orgao),
    CONSTRAINT fk_registro_precos_orgao_1           FOREIGN KEY                      (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno)
                                                    REFERENCES tcemg.registro_precos (cod_entidade, numero_registro_precos, exercicio, interno),
    CONSTRAINT fk_registro_precos_orgao_2           FOREIGN KEY                      (exercicio_unidade, num_unidade, num_orgao)
                                                    REFERENCES orcamento.unidade     (exercicio        , num_unidade, num_orgao)
);
GRANT ALL ON tcemg.registro_precos_orgao TO urbem;


CREATE TABLE tcemg.registro_precos_orgao_item(
    cod_entidade                INTEGER         NOT NULL,
    numero_registro_precos      INTEGER         NOT NULL,
    exercicio_registro_precos   CHAR(4)         NOT NULL,
    interno                     BOOLEAN         NOT NULL,
    exercicio_unidade           CHAR(4)         NOT NULL,
    num_unidade                 INTEGER         NOT NULL,
    num_orgao                   INTEGER         NOT NULL,
    cod_lote                    INTEGER         NOT NULL,
    cod_item                    INTEGER         NOT NULL,
    cgm_fornecedor              INTEGER         NOT NULL,
    quantidade                  NUMERIC(14,4)   NOT NULL,
    CONSTRAINT pk_registro_precos_orgao_item    PRIMARY KEY                            (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, exercicio_unidade, num_unidade, num_orgao),
    CONSTRAINT fk_registro_precos_orgao_item_1  FOREIGN KEY                            (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, exercicio_unidade, num_unidade, num_orgao)
                                                REFERENCES tcemg.registro_precos_orgao (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, exercicio_unidade, num_unidade, num_orgao),
    CONSTRAINT fk_registro_precos_orgao_item_2  FOREIGN KEY                            (cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, cod_lote, cod_item, cgm_fornecedor)
                                                REFERENCES tcemg.item_registro_precos  (cod_entidade, numero_registro_precos, exercicio, interno, cod_lote, cod_item, cgm_fornecedor)
);
GRANT ALL ON tcemg.registro_precos_orgao_item TO urbem;


----------------
-- Ticket #22507
----------------

ALTER TABLE patrimonio.bem_comprado ADD COLUMNB caminho_nf VARCHAR (100);

