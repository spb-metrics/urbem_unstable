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
    * Script de função PLPGSQL - Arquivo de mapeamento para a função que busca os dados do recurso de alienacao do ativo
    * Data de Criação: 05/02/2015
    * @author Lisiane da Rosa Morais
    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_recurso_alienacao_ativo(varchar, integer ,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inMes                   ALIAS FOR $2;
    stCodEntidades          ALIAS FOR $3;
    
    stCodRecursos       VARCHAR   := '';
    stSql               VARCHAR   := '';
    dtInicial           varchar   := ''; 
    dtFinal             varchar   := '';
    dtInicioAnterior    VARCHAR   := '';
    dtFimAnterior       VARCHAR   := '';
    stExercicioAnterior VARCHAR   := '';
    dtInicioAno         VARCHAR   := '';
    
    crCursor            REFCURSOR;
    arCodEntidade       VARCHAR[];
    stNomEntidade       VARCHAR := '';
    reRegistro          RECORD;

    arDatas varchar[] ;

BEGIN
        dtInicioAno := '01/01/'||stExercicio;
        arDatas := publico.mes ( stExercicio, inMes );
        dtInicial := arDatas [ 0 ];
        dtFinal   := arDatas [ 1 ];
        
        stExercicioAnterior := TO_CHAR((TO_DATE(stExercicio, 'yyyy') - 1),'yyyy');
        
        dtInicioAnterior := '01/01/'||stExercicioAnterior;
        dtFimAnterior := '31/12/'||stExercicioAnterior;
        arCodEntidade := string_to_array(stCodEntidades,',');

     CREATE TEMPORARY TABLE tmp_retorno(
        mes                 INTEGER,
        saldo_anterior      NUMERIC(14,2),
        rec_realizada       NUMERIC(14,2),
        desp_emp            NUMERIC(14,2),
        desp_liq            NUMERIC(14,2),
        desp_paga           NUMERIC(14,2),
        cod_vinc            INTEGER,
        cod_entidade        INTEGER
    );
            
    ----------------------------------------
    -- Recupera os recursos para a consulta
    ----------------------------------------
    SELECT ARRAY_TO_STRING(ARRAY( SELECT cod_recurso
                                    FROM stn.recurso_rreo_anexo_14
                                   WHERE exercicio = stExercicio
                                ),',' )
    INTO stCodRecursos;
        
     ---------------------------------
    -- Recupera o nome das entidades
    ---------------------------------
    stSql := '
        CREATE TEMPORARY TABLE tmp_entidade AS (
            SELECT cod_entidade
                 , nom_cgm AS nom_entidade
              FROM orcamento.entidade
        INNER JOIN sw_cgm
                ON entidade.numcgm = sw_cgm.numcgm
             WHERE entidade.exercicio = ''' || stExercicio || '''
               AND entidade.cod_entidade IN (' || stCodEntidades || ')
        )      
    ';

    EXECUTE stSql;    
      
    -----------------------------
    -- Recupera o saldo anterior
    -----------------------------
    
    stSql := 'CREATE TEMPORARY TABLE tmp_saldo_anterior AS (
                        SELECT SUM(valor) AS saldo_anterior
                             , cod_entidade
                          FROM (SELECT SUM ( valor_lancamento.vl_lancamento )AS valor
                                     , valor_lancamento.cod_entidade
                                  FROM contabilidade.plano_conta																		
                                  JOIN contabilidade.plano_analitica																	
                                    ON ( plano_conta.exercicio = plano_analitica.exercicio												
                                   AND   plano_conta.cod_conta = plano_analitica.cod_conta )											
                                  JOIN contabilidade.conta_credito																		
                                    ON ( plano_analitica.exercicio = conta_credito.exercicio											
                                   AND   plano_analitica.cod_plano = conta_credito.cod_plano )											
                                  JOIN contabilidade.plano_recurso																		
                                    ON ( plano_analitica.exercicio = plano_recurso.exercicio											
                                   AND   plano_analitica.cod_plano = plano_recurso.cod_plano )											
                                  JOIN contabilidade.valor_lancamento																	
                                    ON ( conta_credito.exercicio    = valor_lancamento.exercicio										
                                   AND   conta_credito.cod_entidade = valor_lancamento.cod_entidade									
                                   AND   conta_credito.tipo         = valor_lancamento.tipo											
                                   AND   conta_credito.cod_lote     = valor_lancamento.cod_lote										
                                   AND   conta_credito.sequencia    = valor_lancamento.sequencia										
                                   AND   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )									
                                  JOIN contabilidade.lote																				
                                    ON ( valor_lancamento.exercicio    = lote.exercicio												
                                   AND   valor_lancamento.cod_entidade = lote.cod_entidade												
                                   AND   valor_lancamento.tipo         = lote.tipo														
                                   AND   valor_lancamento.cod_lote     = lote.cod_lote )												
                                 WHERE  plano_conta.exercicio =  '''|| stExercicioAnterior ||'''									
                                   AND plano_conta.cod_estrutural LIKE ''1.1.1.%''														
                                   AND lote.dt_lote BETWEEN to_date( '''|| dtInicioAnterior ||''' , ''dd/mm/yyyy'' ) 			
                                        AND   to_date( '''|| dtFimAnterior ||''' , ''dd/mm/yyyy'' )				
                                   AND plano_recurso.cod_recurso IN ( '|| stCodRecursos ||' )										
                                   AND valor_lancamento.cod_entidade IN ( '|| stCodEntidades ||' )
                              GROUP BY valor_lancamento.cod_entidade 
                     UNION ALL 																											
                                SELECT SUM ( valor_lancamento.vl_lancamento ) AS valor
                                      , valor_lancamento.cod_entidade														
                                  FROM contabilidade.plano_conta plano_conta															
                                  JOIN contabilidade.plano_analitica																	
                                    ON ( plano_conta.exercicio = plano_analitica.exercicio 											
                                   AND   plano_conta.cod_conta = plano_analitica.cod_conta ) 											
                                  JOIN contabilidade.conta_debito																		
                                    ON ( plano_analitica.exercicio = conta_debito.exercicio											
                                   AND   plano_analitica.cod_plano = conta_debito.cod_plano )											
                                  JOIN contabilidade.plano_recurso																		
                                    ON ( plano_analitica.exercicio = plano_recurso.exercicio											
                                   AND   plano_analitica.cod_plano = plano_recurso.cod_plano )											
                                  JOIN contabilidade.valor_lancamento 																	
                                    ON ( conta_debito.exercicio    = valor_lancamento.exercicio 										
                                   AND   conta_debito.cod_entidade = valor_lancamento.cod_entidade 									
                                   AND   conta_debito.tipo         = valor_lancamento.tipo         									
                                   AND   conta_debito.cod_lote     = valor_lancamento.cod_lote     									
                                   AND   conta_debito.sequencia    = valor_lancamento.sequencia    									
                                   AND   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )										
                                  JOIN contabilidade.lote 																				
                                    ON ( valor_lancamento.exercicio    = lote.exercicio     											
                                   AND   valor_lancamento.cod_entidade = lote.cod_entidade  											
                                   AND   valor_lancamento.tipo         = lote.tipo          											
                                   AND   valor_lancamento.cod_lote     = lote.cod_lote )												
                                 WHERE plano_conta.exercicio =  '''|| stExercicioAnterior ||''' 									
                                   AND plano_conta.cod_estrutural like ''1.1.1.%''														
                                   AND lote.dt_lote BETWEEN to_date( '''|| dtInicioAnterior ||''' , ''dd/mm/yyyy'' ) 			
                                                      AND   to_date( '''|| dtFimAnterior ||''' , ''dd/mm/yyyy'' )				
                                   AND plano_recurso.cod_recurso IN ( '|| stCodRecursos ||' )										
                                   AND valor_lancamento.cod_entidade IN ( '|| stCodEntidades ||' )
                              GROUP BY valor_lancamento.cod_entidade 
                     ) AS saldo
                GROUP BY saldo.cod_entidade )';
   EXECUTE stSql; 


        
    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
                , ore.cod_recurso    as recurso
                , ore.cod_entidade   as cod_entidade
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE

                    ore.exercicio       = '|| quote_literal(stExercicio) ||' ';
                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN (' || stCodEntidades || ') ';
                end if;

            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''D''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
                , ore.cod_recurso    as recurso
                , ore.cod_entidade   as cod_entidade
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio       = '|| quote_literal(stExercicio) ||' ';  

                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN (' || stCodEntidades || ') ';
                end if;
            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio


                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''C''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo ) '; 

        EXECUTE stSql;        


    -------------------------------------
    --Recupera Receitas Realizadas
    -------------------------------------
stSql := '
    CREATE TEMPORARY TABLE tmp_receitas_realizadas AS (
        SELECT tbl.cod_entidade,
               coalesce(SUM(tbl.receitas_realizadas),0.00)*-1 as receitas_realizadas
          FROM( SELECT tmp_valor.cod_entidade
                     , tmp_valor.valor AS receitas_realizadas
                  FROM tmp_valor   
                 WHERE tmp_valor.cod_estrutural like ''2.%''
                   AND tmp_valor.recurso IN ( ' || stCodRecursos || ' )
                   AND tmp_valor.data BETWEEN to_date(''' || dtInicial || ''',''dd/mm/yyyy'') AND 
                                   to_date(''' || dtFinal || ''',''dd/mm/yyyy'')
                   AND publico.fn_nivel(tmp_valor.cod_estrutural) >       1
                   AND publico.fn_nivel(tmp_valor.cod_estrutural) <=      3
               ) as tbl
      GROUP BY tbl.cod_entidade)';
    EXECUTE stSql;
    
  
    -------------------------------------
    --Recupera Despesas Empenhadas
    -------------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT coalesce(sum(vl_total), 0.00) as vl_empenhado 
    	     , e.cod_entidade	
          FROM orcamento.conta_despesa ocd 
    INNER JOIN orcamento.despesa ode
            ON ode.exercicio = ocd.exercicio
           AND ode.cod_conta = ocd.cod_conta 
    INNER JOIN empenho.pre_empenho_despesa ped
            ON ped.exercicio = ode.exercicio
           AND ped.cod_despesa = ode.cod_despesa 
    INNER JOIN empenho.pre_empenho pe
            ON ped.exercicio = pe.exercicio
           AND ped.cod_pre_empenho = pe.cod_pre_empenho 
    INNER JOIN empenho.item_pre_empenho ipe
            ON ipe.cod_pre_empenho = pe.cod_pre_empenho
           AND ipe.exercicio = pe.exercicio 
    INNER JOIN empenho.empenho e
            ON e.exercicio = pe.exercicio
           AND e.cod_pre_empenho = pe.cod_pre_empenho 
         WHERE e.exercicio = ''' || stExercicio || '''
           AND e.cod_entidade IN (' || stCodEntidades || ')
           AND ode.cod_recurso IN (' || stCodRecursos || ')
           AND e.dt_empenho BETWEEN to_date(''' || dtInicial || ''',''dd/mm/yyyy'')
                                AND to_date(''' || dtFinal || ''',''dd/mm/yyyy'')
           AND (ocd.cod_estrutural LIKE ''3.%'' OR ocd.cod_estrutural LIKE ''4.%'' )
           AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
      GROUP BY e.cod_entidade )
        ';
    EXECUTE stSql;
    
    ------------------------
    --Recupera Despesas Empenhadas Anuladas
    -----------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_empenhado_anulado AS (
        SELECT coalesce(sum(eai.vl_anulado), 0.00) as valor_empenhado_anulado
             , e.cod_entidade
          FROM orcamento.conta_despesa ocd 
    INNER JOIN orcamento.despesa ode
            ON ode.exercicio = ocd.exercicio
           AND ode.cod_conta = ocd.cod_conta 
    INNER JOIN empenho.pre_empenho_despesa ped
            ON ped.exercicio = ode.exercicio
           AND ped.cod_despesa = ode.cod_despesa 
    INNER JOIN empenho.pre_empenho pe
            ON ped.exercicio = pe.exercicio
           AND ped.cod_pre_empenho = pe.cod_pre_empenho
    INNER JOIN empenho.empenho e
            ON e.exercicio = pe.exercicio
           AND e.cod_pre_empenho = pe.cod_pre_empenho 
    INNER JOIN empenho.empenho_anulado ea
            ON ea.exercicio = e.exercicio
           AND ea.cod_entidade = e.cod_entidade
           AND ea.cod_empenho = e.cod_empenho 		
    INNER JOIN empenho.empenho_anulado_item eai
            ON eai.exercicio = ea.exercicio
           AND eai.cod_entidade = ea.cod_entidade
           AND eai.cod_empenho = ea.cod_empenho
           AND eai.timestamp = ea.timestamp  
        WHERE e.exercicio = ''' || stExercicio || '''
           AND e.cod_entidade IN (' || stCodEntidades || ')
           AND ode.cod_recurso IN (' || stCodRecursos || ')
           AND e.dt_empenho BETWEEN to_date(''' || dtInicial || ''',''dd/mm/yyyy'')
                                AND to_date(''' || dtFinal || ''',''dd/mm/yyyy'')
           AND (ocd.cod_estrutural LIKE ''3.%'' OR ocd.cod_estrutural LIKE ''4.%'' )
           AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
      GROUP BY e.cod_entidade )';
    EXECUTE stSql;
   
    ----------------------------------
    -- Recupera Despesas Liquidas
    ----------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT coalesce(sum(vl_total), 0.00) as vl_liquidado
             , e.cod_entidade
	  FROM empenho.pre_empenho pe 
     LEFT JOIN ( SELECT ped.exercicio, 
			ped.cod_pre_empenho, 
			cd.cod_estrutural,
			d.cod_recurso 
		   FROM orcamento.conta_despesa cd 
	     INNER JOIN empenho.pre_empenho_despesa ped
                     ON ped.cod_conta   = cd.cod_conta
                    AND ped.exercicio   = cd.exercicio 
	     INNER JOIN orcamento.despesa d
                     ON ped.cod_despesa = d.cod_despesa
                    AND ped.exercicio   = d.exercicio 
		  WHERE ped.exercicio = ''' || stExercicio || '''
	    ) AS pedcd
            ON pe.exercicio = pedcd.exercicio
           AND pe.cod_pre_empenho = pedcd.cod_pre_empenho 
    INNER JOIN empenho.empenho e
            ON e.exercicio = pe.exercicio
           AND e.cod_pre_empenho = pe.cod_pre_empenho 
    INNER JOIN empenho.nota_liquidacao nl
            ON nl.exercicio_empenho = e.exercicio
           AND nl.cod_entidade = e.cod_entidade
           AND nl.cod_empenho = e.cod_empenho 
    INNER JOIN empenho.nota_liquidacao_item nli
            ON nli.exercicio = nl.exercicio
           AND nli.cod_entidade = nl.cod_entidade
           AND nli.cod_nota = nl.cod_nota 
	 WHERE e.exercicio = ''' || stExercicio || '''
           AND e.cod_entidade IN (' || stCodEntidades || ')
           AND pedcd.cod_recurso IN (' || stCodRecursos || ')
           AND nl.dt_liquidacao BETWEEN to_date(''' || dtInicial || ''', ''dd/mm/yyyy'') AND 
					to_date(''' || dtFinal || ''', ''dd/mm/yyyy'') 
	   AND (pedcd.cod_estrutural LIKE ''3.%'' OR pedcd.cod_estrutural LIKE ''4.%'' ) 
	   AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
      GROUP BY e.cod_entidade )
    ';
    EXECUTE stSql;
    
    ----------------------------------
    -- Despesas Liquidadas Estornadas
    ---------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_estornado_liquidacao AS (
         SELECT coalesce(sum(vl_total), 0.00) as vl_liquidado_estornado
                  , e.cod_entidade
  	     FROM empenho.pre_empenho pe 
          LEFT JOIN ( SELECT ped.exercicio, 
  	   		     ped.cod_pre_empenho, 
  	   		     cd.cod_estrutural,
  	   		     d.cod_recurso 
  	   	        FROM orcamento.conta_despesa cd 
  	          INNER JOIN empenho.pre_empenho_despesa ped
                          ON ped.cod_conta   = cd.cod_conta
                         AND ped.exercicio   = cd.exercicio 
  	          INNER JOIN orcamento.despesa d
                          ON ped.cod_despesa = d.cod_despesa
                         AND ped.exercicio   = d.exercicio 
  	   	       WHERE ped.exercicio = ''' || stExercicio || '''
  	       ) AS pedcd
                 ON pe.exercicio = pedcd.exercicio
                AND pe.cod_pre_empenho = pedcd.cod_pre_empenho 
         INNER JOIN empenho.empenho e
                 ON e.exercicio = pe.exercicio
                AND e.cod_pre_empenho = pe.cod_pre_empenho 
         INNER JOIN empenho.nota_liquidacao nl
                 ON nl.exercicio_empenho = e.exercicio
                AND nl.cod_entidade = e.cod_entidade
                AND nl.cod_empenho = e.cod_empenho 
         INNER JOIN empenho.nota_liquidacao_item nli
                 ON nli.exercicio = nl.exercicio
                AND nli.cod_entidade = nl.cod_entidade
                AND nli.cod_nota = nl.cod_nota
         INNER JOIN empenho.nota_liquidacao_item_anulado nlia
                 ON nli.exercicio = nlia.exercicio
                AND nli.cod_nota = nlia.cod_nota
                AND nli.cod_entidade = nlia.cod_entidade
                AND nli.num_item = nlia.num_item
                AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                AND nli.exercicio_item = nlia.exercicio_item        
  	      WHERE e.exercicio = ''' || stExercicio || '''
                AND e.cod_entidade IN (' || stCodEntidades || ')
                AND pedcd.cod_recurso IN (' || stCodRecursos || ')
                AND to_date( to_char( nlia.timestamp,''dd/mm/yyyy''), ''dd/mm/yyyy'' ) BETWEEN to_date(''' || dtInicial || ''', ''dd/mm/yyyy'') AND 
  	   				to_date(''' || dtFinal || ''', ''dd/mm/yyyy'') 
  	      AND (pedcd.cod_estrutural LIKE ''3.%'' OR pedcd.cod_estrutural LIKE ''4.%'' ) 
  	      AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
           GROUP BY e.cod_entidade )';
    EXECUTE stSql;
    
    
   --------------------------------
    -- Recupera a despesa paga
   --------------------------------
   stSql := 'CREATE TABLE  tmp_pago AS (
               SELECT nota_liquidacao_paga.vl_pago as vl_pago
                    , despesa.cod_entidade as cod_entidade
                 FROM orcamento.despesa 
                 JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                   ON ( ORU.cod_recurso = despesa.cod_recurso
                  AND   ORU.exercicio   = despesa.exercicio )
                    , orcamento.conta_despesa 
                    , empenho.pre_empenho_despesa 
                    , empenho.empenho 
                    , empenho.pre_empenho 
                    , empenho.nota_liquidacao 
                    , empenho.nota_liquidacao_paga
                WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
 
                  AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                  AND despesa.exercicio   = pre_empenho_despesa.exercicio
 
                  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
 
                  AND pre_empenho.exercicio       = empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
 
                  AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                  AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade
 
                  AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                  AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                  AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                  AND nota_liquidacao_paga.timestamp BETWEEN to_date(''' || dtInicial || ''', ''dd/mm/yyyy'')
                                                         AND to_date(''' || dtFinal || ''', ''dd/mm/yyyy'')
 
                  AND empenho.exercicio              = '''|| stExercicio || '''
                  AND nota_liquidacao_paga.exercicio = '''|| stExercicio || ''' 
                  AND despesa.cod_entidade          IN (' || stCodEntidades || ')
                  AND despesa.cod_recurso           IN (' || stCodRecursos || ')
                  AND conta_despesa.cod_estrutural like ''4.%'')' ;

              EXECUTE stSql;


   stSql := 'CREATE TABLE tmp_pago_anulado  AS(
              SELECT nota_liquidacao_paga_anulada.vl_anulado as vl_anulado
                   , despesa.cod_entidade as cod_entidade
                FROM orcamento.despesa 
                JOIN orcamento.recurso(''' || stExercicio || ''') as ORU
                  ON ( ORU.cod_recurso = despesa.cod_recurso
                 AND   ORU.exercicio   = despesa.exercicio )
                   , orcamento.conta_despesa 
                   , empenho.pre_empenho_despesa 
                   , empenho.empenho
                   , empenho.pre_empenho 
                   , empenho.nota_liquidacao 
                   , empenho.nota_liquidacao_paga
                   , empenho.nota_liquidacao_paga_anulada 
               WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                 AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                 AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                 AND despesa.exercicio   = pre_empenho_despesa.exercicio

                 AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                 AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                 AND pre_empenho.exercicio       = empenho.exercicio
                 AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                 AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                 AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                 AND empenho.cod_entidade = nota_liquidacao.cod_entidade

                 AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                 AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                 AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade

                 AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                 AND nota_liquidacao_paga.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                 AND nota_liquidacao_paga.exercicio    = nota_liquidacao_paga_anulada.exercicio
                 AND nota_liquidacao_paga.timestamp    = nota_liquidacao_paga_anulada.timestamp
                 AND nota_liquidacao_paga_anulada.timestamp_anulada BETWEEN to_date(''' || dtInicial || ''', ''dd/mm/yyyy'')
                                                                        AND to_date(''' || dtFinal || ''', ''dd/mm/yyyy'')

                 AND empenho.exercicio              = '''|| stExercicio || '''
                 AND nota_liquidacao_paga.exercicio = '''|| stExercicio || ''' 
                 AND despesa.cod_entidade          IN (' || stCodEntidades || ')
                 AND despesa.cod_recurso           IN (' || stCodRecursos || ')
                 AND conta_despesa.cod_estrutural like ''4.%'' )' ;
              EXECUTE stSql;
        --------------------------------
        -- Fim Recupera a despesa paga
        --------------------------------
        
   ---------------------------------------
    -- Faz as consultas para cada entidade 
    ---------------------------------------
    FOR i IN 1..ARRAY_UPPER(arCodEntidade,1) LOOP
        stCodEntidades := arCodEntidade[i];
        SELECT nom_entidade 
          INTO stNomEntidade
          FROM tmp_entidade
         WHERE cod_entidade::VARCHAR = arCodEntidade[i];    

        INSERT INTO tmp_retorno VALUES ( inMes  
                                        , ( SELECT COALESCE(saldo_anterior,0.00)
                                                FROM tmp_saldo_anterior
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i])
                                        , ( SELECT COALESCE(SUM(receitas_realizadas),0.00)*(-1)
                                              FROM tmp_receitas_realizadas
                                             WHERE cod_entidade::VARCHAR = arCodEntidade[i] )
                                         , ( ( SELECT COALESCE(SUM(vl_empenhado), 0.00) 
                                                FROM tmp_empenhado
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(valor_empenhado_anulado), 0.00)
                                                FROM tmp_empenhado_anulado
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] ) )    
                                         , ( ( SELECT COALESCE(SUM(vl_liquidado), 0.00) 
                                                FROM tmp_liquidado
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(vl_liquidado_estornado), 0.00)
                                                FROM tmp_estornado_liquidacao
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] ) )
                                        , ( ( SELECT COALESCE(SUM(vl_pago), 0.00)
                                                FROM tmp_pago
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] )
                                          - ( SELECT COALESCE(SUM(vl_anulado), 0.00)
                                                FROM tmp_pago_anulado
                                               WHERE cod_entidade::VARCHAR = arCodEntidade[i] ) )
                                        , CASE WHEN (stNomEntidade ILIKE '%prefeitura%')
                                               THEN 1
                                               WHEN (stNomEntidade ILIKE '%camara%' OR stNomEntidade ILIKE '%câmara%')
                                               THEN 3
                                               ELSE 2
                                          END
                                        , arCodEntidade[i]::INTEGER
                                       );           

    END LOOP;
    

stSql := 'SELECT * FROM tmp_retorno';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_receitas_realizadas;
    DROP TABLE tmp_entidade;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_empenhado_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_estornado_liquidacao;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_pago_anulado;
    DROP TABLE tmp_retorno;
    DROP TABLE tmp_saldo_anterior;
    RETURN;
END;
$$ language 'plpgsql';

