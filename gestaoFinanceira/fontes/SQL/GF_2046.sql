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
* Versao 2.04.6
*
* Fabio Bertoldi - 20151105
*
*/

----------------
-- Ticket #23337
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
     ( 3092
     , 56
     , 'FMManterReceitaDespesaExtraRecurso.php'
     , 'configurar'
     , 16
     , 'Configurar Rec./Desp. Extra por Recurso'
     , 'Configurar Rec./Desp. Extra por Fonte de Recurso'
     , TRUE
     );

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 9
     , '2015'
     , 'indicador_contas_extras_recurso'
     , 'f'
     );

CREATE TABLE contabilidade.configuracao_contas_extras (
    exercicio   VARCHAR(4)    NOT NULL,
    cod_conta   INTEGER       NOT NULL,
    CONSTRAINT pk_configuracao_contas_extras   PRIMARY KEY (exercicio, cod_conta),
    CONSTRAINT fk_configuracao_contas_extras_1 FOREIGN KEY (exercicio, cod_conta)
                                               REFERENCES contabilidade.plano_conta (exercicio, cod_conta)
);
GRANT ALL ON contabilidade.configuracao_contas_extras TO urbem;
