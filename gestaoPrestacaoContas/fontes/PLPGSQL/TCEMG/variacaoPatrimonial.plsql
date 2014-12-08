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
Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 30/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    * @package URBEM
    * @subpackage

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_variacao_patrimonial(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN




CREATE TEMPORARY TABLE tmp_arquivo (
          mes         INTEGER
        , deficit     NUMERIC(14,2)
        , superavit   NUMERIC(14,2) 
    );

stSql := '
INSERT INTO tmp_arquivo(mes,deficit,superavit)VALUES( '|| inMes||', 
       
--DEFICIT

 (SELECT
            coalesce(sum(vl.vl_lancamento),0)
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
        AND     EXTRACT(month from lo.dt_lote) = '|| inMes||'
        AND     pc.cod_estrutural like ''3%''),
        

-- SUPERAVIT
    (SELECT
            coalesce(sum(vl.vl_lancamento),0)
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
        AND     EXTRACT(month from lo.dt_lote) = '|| inMes||'
        AND     pc.cod_estrutural like ''4%'' )


)
';





EXECUTE stSql;



stSql := ' SELECT mes, COALESCE(deficit, 0.00),COALESCE(superavit, 0.00)  FROM tmp_arquivo; ';

FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;


END;
$$ LANGUAGE 'plpgsql';

