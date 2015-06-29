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
Revision 1.3  2007/05/24 20:51:57  bruce
corrigido o retorno da pl e feita ligação com a Unidade

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

CREATE OR REPLACE FUNCTION tcmgo.arquivo_afr_exportacao10 (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades         ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    inTipoLancamento    VARCHAR;
    arEntidades         VARCHAR[];
    i                   INTEGER;
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    stEntidadeTCMGO     VARCHAR;
    inOrgao             INTEGER;
    inUnidade           INTEGER;
    intamanhoentidade integer;

BEGIN
    
    CREATE TEMPORARY TABLE tmp_balanco_verificacao_afr( 
        tipo_lancamento        varchar,
        orgao                  varchar,
        unidade                varchar,
        cod_estrutural         varchar,
        nivel                  integer,
        nom_conta              varchar,
        cod_sistema            integer,
        indicador_superavit    char(12),
        vl_saldo_anterior      numeric,
        vl_saldo_debitos       numeric,
        vl_saldo_creditos      numeric,
        vl_saldo_atual         numeric
    );

    arEntidades := string_to_array(stEntidades,',');

    FOR i IN 1..array_length(arEntidades,1)
    LOOP
        SELECT sw_cgm.nom_cgm
        INTO stEntidadeTCMGO
        FROM orcamento.entidade                   
            , sw_cgm                               
        WHERE entidade.numcgm = sw_cgm.numcgm
        AND entidade.exercicio = stExercicio
        AND entidade.cod_entidade = arEntidades[i]::integer;

        --Quando forem lançamentos da entidade Prefeitura utilizar o código 04
        IF (regexp_matches(lower(stEntidadeTCMGO),'(prefeitura)')) IS NOT NULL THEN
            inTipoLancamento := '04';
        --Quando forem lançamentos da entidade Camara será o código 01
        ELSEIF (regexp_matches(lower(stEntidadeTCMGO),'(câmara)|(camara)')) IS NOT NULL THEN
            inTipoLancamento := '01';
        --Quando forem lançamentos da entidade Instituto utilizar o código 05        
        ELSEIF (regexp_matches(lower(stEntidadeTCMGO),'(instituto)')) IS NOT NULL THEN
            inTipoLancamento := '05';
        --Quando forem lançamentos da entidade Consórcio utilizar o código 06
        ELSEIF (regexp_matches(lower(stEntidadeTCMGO),'(consórcio)|(consorcio)')) IS NOT NULL THEN
            inTipoLancamento := '06';
        ELSE 
            inTipoLancamento := '00';
        END IF;

        --Buscar da configuração o orgao unidade configuracado em Gestão Prestação de Contas :: TCM - GO :: Configuração :: Configurar Órgão/Unidade das Contas Contábeis
        --Campo orgao e unidade serão com o mesmo valor
        SELECT  COALESCE(num_orgao,0) as num_orgao
                , COALESCE(num_unidade,0) as num_unidade
        INTO inOrgao
            ,inUnidade
        FROM tcmgo.configuracao_orgao_unidade 
        WHERE exercicio = stExercicio
        AND cod_entidade = arEntidades[i]::integer;

        stSql := 'INSERT INTO tmp_balanco_verificacao_afr
                    SELECT  '''||inTipoLancamento||''' as tipo_lancamento
                            , '||inOrgao||' as orgao
                            , '||inUnidade||' as unidade
                            , *  
                    FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicio||'''
                                                                , '' cod_entidade IN  ( '||arEntidades[i]||' ) ''
                                                                , '''||stDtInicial||'''
                                                                , '''||stDtFinal||'''
                                                                , ''A''::CHAR
                                                                ) 
                    AS registro(  
                                cod_estrutural          varchar
                                ,nivel                  integer
                                ,nom_conta              varchar
                                ,cod_sistema            integer
                                ,indicador_superavit    char(12)
                                ,vl_saldo_anterior      numeric
                                ,vl_saldo_debitos       numeric
                                ,vl_saldo_creditos      numeric
                                ,vl_saldo_atual         numeric
                                )
                    WHERE cod_estrutural SIMILAR TO ''1.1.2%|1.1.3%|1.1.4%''
                    AND cod_sistema <> 4 ; ';
        
        EXECUTE stSql;

    END LOOP;

    stSql := ' SELECT * FROM tmp_balanco_verificacao_afr ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP TABLE tmp_balanco_verificacao_afr;

    RETURN;
END;
$$ LANGUAGE 'plpgsql'