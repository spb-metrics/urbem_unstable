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
/**
Arquivo de mapeamento para a função que busca os dados de variação patrimonial
    * Data de Criação   : 15/10/2013

    * @author Analista      
    * @author Desenvolvedor Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage

    $Id: $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_passivo_perm(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN 

CREATE TEMPORARY TABLE tmp_arquivo (
          mes                   INTEGER
        , codTipo               INTEGER
        , valorEmp              NUMERIC
        , valorTransConcedidas  NUMERIC
        , valorProvisaoRPPS     NUMERIC
    );

stSql := '
INSERT INTO tmp_arquivo(mes,valorEmp,valorTransConcedidas,valorProvisaoRPPS)VALUES(''12'' ,

 (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade        
        AND     la.tipo <> ''I''
        AND     pc.exercicio  = '''|| stExercicio|| '''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like  ''2.1.2%''
        AND     pc.indicador_superavit like ''p%''),

 (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     la.tipo <> ''I''
        AND     pc.exercicio  = '''|| stExercicio|| '''  
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like  ''3.5%''
        AND     pc.indicador_superavit like ''p%''),

  (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade

        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     la.tipo <> ''I''
        AND     pc.exercicio  = '''|| stExercicio|| '''  
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like  ''2.2.7.2%''
        AND     pc.indicador_superavit like ''p%'')
)';

EXECUTE stSql;

--lançamento a débito com sinal positivo, então será com o codtipo 01 - acréscimo)
--lançamento a débito com sinal negativo, então será com o codtipo 02 - redução)
stSql := '  SELECT  mes
                    , CASE WHEN SIGN(valorEmp) > 0.00 THEN
                            valorEmp
                        ELSE
                            0.00
                    END as valorEmp
                    , CASE WHEN SIGN(valorTransConcedidas) > 0 THEN
                            valorTransConcedidas
                        ELSE
                            0.00
                    END as valorTransConcedidas
                    , CASE WHEN SIGN(valorProvisaoRPPS) > 0 THEN
                            valorProvisaoRPPS
                        ELSE
                            0.00
                    END as valorProvisaoRPPS
                    , 1 as codTipo
            FROM tmp_arquivo

        UNION

            SELECT  mes
                    , CASE WHEN SIGN(valorEmp) < 0 THEN
                            valorEmp
                        ELSE
                            0.00
                    END as valorEmp
                    , CASE WHEN SIGN(valorTransConcedidas) < 0 THEN
                            valorTransConcedidas
                        ELSE
                            0.00
                    END as valorTransConcedidas
                    , CASE WHEN SIGN(valorProvisaoRPPS) < 0 THEN
                            valorProvisaoRPPS
                        ELSE
                            0.00
                    END as valorProvisaoRPPS
                    , 2 as codTipo
            FROM tmp_arquivo
 
        ';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_arquivo;

RETURN;

END;
$$ LANGUAGE 'plpgsql';



