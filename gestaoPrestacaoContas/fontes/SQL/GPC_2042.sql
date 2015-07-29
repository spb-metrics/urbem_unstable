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
* Versao 2.04.2
*
* Fabio Bertoldi - 20150728
*
*/

----------------
-- Ticket #23127
----------------

ALTER TABLE tcmba.tipo_responsavel_ordenador ALTER COLUMN descricao TYPE VARCHAR(50);

INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 1, 'Prefeito/Presidente'                     );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 2, 'Secretário'                              );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 3, 'Tesoureiro/Pagador'                      );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 4, 'Responsável Bens Patrimoniais'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 5, 'Responsável Bens Almoxarifado'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 6, 'Presidente Comissão Permanente Licitação');
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 7, 'Chefe Órgão Controle Interno'            );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 9, 'Pregoeiro'                               );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (10, 'Pregoeiro Substituto'                    );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (11, 'Equipe de Apoio (Pregão)'                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (12, 'Vice-Prefeito'                           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (13, 'Retificador de Despesas'                 );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (14, 'Atuário'                                 );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (15, 'Contador'                                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (16, 'Secretário de Finanças'                  );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (17, 'Responsável por Fiscalizar Obra'         );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (18, 'Primeiro Secretário da Câmara'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (19, 'Vereador'                                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (20, 'Delegação do Controle Interno'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (21, 'Participante de Conselho/Comite'         );

