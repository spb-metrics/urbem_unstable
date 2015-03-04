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
* Gelson Gonçalves - 20150211
*
*/

----------------
-- Ticket #22676
----------------

CREATE TABLE frota.veiculo_cessao (
    id                  INTEGER NOT NULL,
    cod_veiculo         INTEGER NOT NULL,
    cod_processo        INTEGER NOT NULL,
    exercicio           VARCHAR(4) NOT NULL,
    cgm_cedente         INTEGER NOT NULL,
    dt_inicio           DATE,
    dt_termino          DATE,
    CONSTRAINT pk_veiculo_cessao    PRIMARY KEY (id),
    CONSTRAINT fk_veiculo_cessao_1  FOREIGN KEY (cod_veiculo)
                                    REFERENCES frota.veiculo (cod_veiculo),
    CONSTRAINT fk_veiculo_cessao_2  FOREIGN KEY (cod_processo,exercicio)
                                    REFERENCES sw_processo (cod_processo,ano_exercicio),
    CONSTRAINT fk_veiculo_cessao_3  FOREIGN KEY (cgm_cedente)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm)

);

GRANT ALL ON frota.veiculo_cessao TO GROUP urbem;

----------------
-- Ticket #22677
----------------

CREATE TABLE frota.veiculo_locacao (
    id                  INTEGER NOT NULL,
    cod_veiculo         INTEGER NOT NULL,
    cod_processo        INTEGER NOT NULL,
    ano_exercicio       VARCHAR(4) NOT NULL,
    cgm_locatario       INTEGER NOT NULL,
    dt_contrato         DATE NOT NULL,
    dt_inicio           DATE NOT NULL,
    dt_termino          DATE NOT NULL,
    exercicio           VARCHAR(4) NOT NULL,
    cod_entidade        INTEGER NOT NULL,
    cod_empenho         INTEGER NOT NULL,
    vl_locacao          NUMERIC(14,2) NOT NULL,
    CONSTRAINT pk_veiculo_locacao   PRIMARY KEY (id),
    CONSTRAINT fk_veiculo_locacao_1 FOREIGN KEY (cod_veiculo)
                                    REFERENCES frota.veiculo (cod_veiculo),
    CONSTRAINT fk_veiculo_locacao_2 FOREIGN KEY (cod_processo,ano_exercicio)
                                    REFERENCES sw_processo (cod_processo,ano_exercicio),
    CONSTRAINT fk_veiculo_locacao_3 FOREIGN KEY (cgm_locatario)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm),
    CONSTRAINT fk_veiculo_locacao_4 FOREIGN KEY (exercicio,cod_entidade,cod_empenho)
                                    REFERENCES empenho.empenho (exercicio,cod_entidade,cod_empenho)
);

GRANT ALL ON frota.veiculo_locacao TO GROUP urbem;

