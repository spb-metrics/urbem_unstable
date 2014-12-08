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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 17524 $
* $Name$
* $Author: cako $
* $Date: 2006-11-09 13:34:45 -0200 (Qui, 09 Nov 2006) $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

/*
$Log$
Revision 1.14  2006/11/09 15:34:45  cako
Bug #6787#

Revision 1.13  2006/10/27 17:28:23  cako
Bug #6787#

Revision 1.12  2006/07/18 20:02:10  eduardo
Bug #6556#

Revision 1.11  2006/07/14 17:58:30  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.10  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.pcasp_fixacao_desp_orcamentaria(VARCHAR, VARCHAR, VARCHAR, VARCHAR, CHAR) RETURNS SETOF RECORD AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    chEstilo            ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    reAux               RECORD;
    inTeste             INTEGER := 0;
    arRetorno           NUMERIC[];

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la
                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                ' || stFiltro ;
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio

                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                ' || stFiltro ;
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);


    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) =  '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN to_date( '|| quote_literal('01/01/'|| stExercicio) ||',''dd/mm/yyyy'') AND to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')-1';
    END IF;
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento || '
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);

    --Verifica estilo de relatório (Analítico ou Sintético)
    IF chEstilo = 'S' THEN
        stSql := ' SELECT
                         pc.cod_estrutural
                        ,publico.fn_nivel(pc.cod_estrutural) as nivel
                        ,pc.nom_conta
                        ,sc.cod_sistema
                        ,pc.indicador_superavit
                        ,0.00 as vl_saldo_anterior
                        ,0.00 as vl_saldo_debitos
                        ,0.00 as vl_saldo_creditos
                        ,0.00 as vl_saldo_atual
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.exercicio = ' || quote_literal(stExercicio) || '
                      AND   pc.exercicio   = sc.exercicio
                      AND   pc.cod_sistema = sc.cod_sistema

                      AND   NOT EXISTS ( SELECT 1
                                           FROM contabilidade.plano_analitica c_pa
                                          WHERE c_pa.cod_conta = pc.cod_conta
                                            AND c_pa.exercicio = pc.exercicio
                                            AND c_pa.exercicio = ' || quote_literal(stExercicio) || '
                                       )
                    ORDER BY cod_estrutural ';
    ELSE
        stSql := ' SELECT
                         pc.cod_estrutural
                        ,publico.fn_nivel(pc.cod_estrutural) as nivel
                        ,CAST(CASE WHEN pa.cod_plano IS NULL THEN
                            pc.nom_conta
                         ELSE
                            pa.cod_plano ||''-''|| pc.nom_conta
                         END AS varchar) AS nom_conta
                        ,sc.cod_sistema
                        ,pc.indicador_superavit
                        ,0.00 as vl_saldo_anterior
                        ,0.00 as vl_saldo_debitos
                        ,0.00 as vl_saldo_creditos
                        ,0.00 as vl_saldo_atual
                    FROM
                         contabilidade.plano_conta      as pc
                         LEFT JOIN contabilidade.plano_analitica as pa ON(
                                pa.cod_conta = pc.cod_conta
                            and pa.exercicio = pc.exercicio
                         )
                        ,contabilidade.sistema_contabil as sc
                    WHERE   pc.exercicio   = ' || quote_literal(stExercicio) || '
                      AND   pc.exercicio   = sc.exercicio
                      AND   pc.cod_sistema = sc.cod_sistema
                    ORDER BY cod_estrutural ';
    END IF;

    CREATE TEMPORARY TABLE tmp_lancamento (
        cod_estrutural      VARCHAR,
        nivel               VARCHAR,
        nom_conta           VARCHAR,
        cod_sistema         INTEGER,
        indicador_superavit VARCHAR,
        vl_saldo_anterior   NUMERIC,
        vl_saldo_debitos    NUMERIC,
        vl_saldo_creditos   NUMERIC,
        vl_saldo_atual      NUMERIC );

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := contabilidade.fn_totaliza_balancete_verificacao( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_atual    := arRetorno[4];
        
        IF ( reRegistro.vl_saldo_anterior <> 0.00 ) OR
           ( reRegistro.vl_saldo_debitos  <> 0.00 ) OR
           ( reRegistro.vl_saldo_creditos <> 0.00 )
        THEN
            INSERT INTO tmp_lancamento (cod_estrutural, nivel, nom_conta, cod_sistema, indicador_superavit, vl_saldo_anterior, vl_saldo_debitos, vl_saldo_creditos, vl_saldo_atual)
                        VALUES (reRegistro.cod_estrutural, reRegistro.nivel, reRegistro.nom_conta, reRegistro.cod_sistema, reRegistro.indicador_superavit, reRegistro.vl_saldo_anterior,
                                reRegistro.vl_saldo_debitos, reRegistro.vl_saldo_creditos, reRegistro.vl_saldo_atual);
        END IF;
    END LOOP;
    
    CREATE TEMPORARY TABLE tmp_contabeis_a AS
        SELECT * FROM tmp_lancamento WHERE cod_estrutural LIKE '5.2.2.1.0.00.00.%' OR
                                           cod_estrutural LIKE '5.2.2.2.0.00.00.%';
                                           
    CREATE TEMPORARY TABLE tmp_contabeis_b AS
        SELECT * FROM tmp_lancamento WHERE cod_estrutural LIKE '6.2.2.1.0.00.00.%' OR
                                           cod_estrutural LIKE '6.2.2.2.0.00.00.%';
                                           
    stSql := 'SELECT tmp_contabeis_a.cod_estrutural AS estrutural_debito,
                     tmp_contabeis_a.nom_conta AS conta_debito,
                     tmp_contabeis_a.vl_saldo_atual AS saldo_debito,
                     tmp_contabeis_b.cod_estrutural AS estrutural_credito,
                     tmp_contabeis_b.nom_conta AS conta_credito,
                     tmp_contabeis_b.vl_saldo_atual AS saldo_credito
                FROM tmp_contabeis_a
                JOIN tmp_contabeis_b
                  ON tmp_contabeis_a.nivel = tmp_contabeis_b.nivel
                 AND tmp_contabeis_a.cod_sistema = tmp_contabeis_b.cod_sistema
               WHERE substr(tmp_contabeis_a.cod_estrutural,2,6) = substr(tmp_contabeis_b.cod_estrutural,2,6)';
               
    CREATE TEMPORARY TABLE tmp_contabeis (
        cod_estrutural_d    VARCHAR,
        conta_d             VARCHAR,
        saldo_d             NUMERIC,
        cod_estrutural_c    VARCHAR,
        conta_c             VARCHAR,
        saldo_c             NUMERIC
    );
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
            INSERT INTO tmp_contabeis (cod_estrutural_d, conta_d, saldo_d, cod_estrutural_c, conta_c, saldo_c)
                            VALUES (reRegistro.estrutural_debito, reRegistro.conta_debito, reRegistro.saldo_debito, reRegistro.estrutural_credito, reRegistro.conta_credito, reRegistro.saldo_credito);
    END LOOP;
    
    stSql := 'SELECT * FROM tmp_contabeis';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    
    DROP TABLE tmp_lancamento;
    DROP TABLE tmp_contabeis_a;
    DROP TABLE tmp_contabeis_b;
    DROP TABLE tmp_contabeis;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
