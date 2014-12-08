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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
*
* Casos de uso: uc-02.02.11
*/

/*
$Log$
Revision 1.2  2007/05/24 15:33:01  bruce
corrigido o retorno da pl e feita ligação com a Unidade

Revision 1.1  2007/05/18 16:02:42  bruce
*** empty log message ***

Revision 1.2  2007/05/15 20:46:31  bruce
acrescentado o tipo de lançamento

Revision 1.1  2007/05/15 13:43:55  bruce
*** empty log message ***

Revision 1.9  2006/07/14 17:58:30  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.8  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcmgo.ativo_permanente_creditos (varchar, varchar, varchar, varchar, bpchar) RETURNS SETOF RECORD AS
'
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    chEstilo            ALIAS FOR $5;
    stSql               VARCHAR   := '''';
    stSqlComplemento    VARCHAR   := '''';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN

    stSql := ''CREATE TEMPORARY TABLE tmp_debito AS
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
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_debito      as cd
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
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
                    AND     vl.tipo_valor   = ''''D''''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = '' || stExercicio || ''
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                '' || stFiltro ;
    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_credito AS
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
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
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
                    AND     vl.tipo_valor   = ''''C''''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = '' || stExercicio || ''
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                '' || stFiltro ;
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial , ''dd/mm/yyyy'' ) AND   to_date( stDtFinal , ''dd/mm/yyyy'' )
        AND   tipo <> ''I'';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , ''dd/mm/yyyy'' ) AND   to_date( stDtFinal , ''dd/mm/yyyy'' )
        AND   tipo <> ''I'';

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) = ''01/01'' THEN
        stSqlComplemento := '' dt_lote = to_date( '' || quote_literal(stDtInicial) || '','' || quote_literal(''dd/mm/yyyy'') || '') '';
        stSqlComplemento := stSqlComplemento || '' AND tipo = ''||quote_literal(''I'')||'' '';
    ELSE
        stSqlComplemento := '' dt_lote <= to_date( '' || quote_literal(stDtInicial) || '','' || quote_literal(''dd/mm/yyyy'') || '')-1 '';
    END IF;

    stSql := ''CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             '' || stSqlComplemento || ''
       UNION
        SELECT * FROM tmp_credito
        WHERE
             '' || stSqlComplemento || ''
    '';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);

    stSql := '' SELECT
                     pc.cod_estrutural
                    ,publico.fn_nivel(pc.cod_estrutural) as nivel
                    ,pc.nom_conta
                    ,org.num_orgao
                    ,ou.cod_unidade
                    ,0.00 as vl_saldo_anterior
                    ,0.00 as vl_saldo_debitos
                    ,0.00 as vl_saldo_creditos
                    ,0.00 as vl_saldo_atual
                    ,sc.nom_sistema
                    ,ba.tipo_lancamento
                FROM
                     contabilidade.plano_conta      as pc
                    ,contabilidade.sistema_contabil as sc
                    ,contabilidade.plano_analitica  as c_pa
                    ,tcmgo.orgao_plano_banco        as org
                    ,tcmgo.balanco_apcaaaa          as ba
                    ,orcamento.unidade              as ou
                WHERE
                    pc.exercicio = '' || stExercicio || ''
                    AND pc.cod_sistema = sc.cod_sistema
                    AND pc.exercicio   = sc.exercicio
                    AND c_pa.cod_conta = pc.cod_conta
                    AND c_pa.exercicio = pc.exercicio
                    AND org.exercicio  = c_pa.exercicio
                    AND org.cod_plano  = c_pa.exercicio
                    AND ba.cod_plano   = c_pa.cod_plano
                    AND ba.exercicio   = c_pa.exercicio
                    AND org.exercicio  = ou.exercicio
                    AND org.num_orgao  = ou.num_orgao
               ORDER BY sc.nom_sistema, pc.cod_estrutural '';


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
            RETURN NEXT reRegistro;
        END IF;
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

    RETURN;
END;
' LANGUAGE 'plpgsql'
