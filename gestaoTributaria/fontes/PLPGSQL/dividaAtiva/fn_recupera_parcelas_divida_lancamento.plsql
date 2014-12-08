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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_recupera_parcelas_divida_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/


CREATE OR REPLACE FUNCTION divida.fn_recupera_parcelas_divida_lancamento( INTEGER )
RETURNS SETOF RECORD AS '

DECLARE
    
    inCodLancamento ALIAS FOR $1;
    
    cod_parcela_unica 	integer;
	retorno 			varchar := '''';
    reRecord            RECORD;
    stSql               VARCHAR;
    
BEGIN

/* Buscamos parcela unica, se só existir ela */

SELECT
	ap.cod_parcela
INTO
	cod_parcela_unica
FROM
	arrecadacao.parcela as ap
	INNER JOIN arrecadacao.carne
	ON carne.cod_parcela = ap.cod_parcela
WHERE
	cod_lancamento = inCodLancamento
	and ap.cod_lancamento NOT IN
	(
		SELECT	ap.cod_lancamento
		FROM 	arrecadacao.parcela as ap
		WHERE	ap.cod_lancamento = inCodLancamento
				and ap.nr_parcela > 0
	);
 
/* SE O LANCAMENTO SÓ CONTER ESSA PARCELA ÚNICA, UTILIZA O VALOR DELA */

IF	cod_parcela_unica is not null THEN
	stSql := ''
			select
			carne.numeracao
            , carne.cod_convenio
			, carne.exercicio::int
			, ap.cod_parcela
			, alc.cod_calculo
			, calc.cod_credito
			, mon.descricao_credito
			, mon.cod_natureza
			, mon.cod_genero
			, mon.cod_especie
			, (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela))::numeric(14,2) as valor
			from arrecadacao.parcela as ap
			INNER JOIN arrecadacao.carne
			ON carne.cod_parcela = ap.cod_parcela
			INNER JOIN arrecadacao.lancamento_calculo as alc
			ON alc.cod_lancamento = ap.cod_lancamento
			INNER JOIN arrecadacao.calculo as calc
			ON calc.cod_calculo = alc.cod_calculo
			INNER JOIN monetario.credito as mon
			ON mon.cod_credito = calc.cod_credito
                AND mon.cod_natureza = calc.cod_natureza
                AND mon.cod_especie = calc.cod_especie
                AND mon.cod_genero = calc.cod_genero
                
                LEFT JOIN ( SELECT carne.cod_parcela
                      FROM arrecadacao.carne
                      JOIN arrecadacao.pagamento
                        ON pagamento.numeracao    = carne.numeracao
                       AND pagamento.cod_convenio = carne.cod_convenio
                ) AS apag
                ON apag.cod_parcela = carne.cod_parcela
            
            LEFT JOIN   arrecadacao.carne_devolucao as carned
            ON carned.numeracao = carne.numeracao and carned.cod_convenio = carne.cod_convenio
			where alc.cod_lancamento = ''||inCodLancamento||''
			and nr_parcela = 0
            --and carned.numeracao is null
            and case when carned.numeracao is not null then
                        case when 1 < (select count(*) from arrecadacao.carne_devolucao acd where acd.numeracao = carne.numeracao
                                                                                         and acd.cod_convenio = carne.cod_convenio) then
                            false
                        else
                            carned.cod_motivo = 10
                        end
                else
                        true
                end
            AND apag.cod_parcela IS NULL

			order by ap.cod_parcela, alc.cod_calculo
			'';
ELSE 
	stSql :=''
		SELECT
			max(carne.numeracao)::varchar as numeracao
                        , carne.cod_convenio
			, carne.exercicio::int
			, ap.cod_parcela
			, alc.cod_calculo
			, calc.cod_credito
			, mon.descricao_credito
			, mon.cod_natureza
			, mon.cod_genero
			, mon.cod_especie
			, (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela))::numeric(14,2) as valor
                        , (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)) as valor_exato
		FROM
            arrecadacao.parcela as ap
			INNER JOIN arrecadacao.carne
			ON carne.cod_parcela = ap.cod_parcela
			INNER JOIN arrecadacao.lancamento_calculo as alc
			ON alc.cod_lancamento = ap.cod_lancamento
			INNER JOIN arrecadacao.calculo as calc
			ON calc.cod_calculo = alc.cod_calculo
			INNER JOIN monetario.credito as mon
			ON mon.cod_credito = calc.cod_credito
                AND mon.cod_natureza = calc.cod_natureza
                AND mon.cod_especie = calc.cod_especie
                AND mon.cod_genero = calc.cod_genero
                        
                        LEFT JOIN ( SELECT carne.cod_parcela
			      FROM arrecadacao.carne
			      JOIN arrecadacao.pagamento
			        ON pagamento.numeracao    = carne.numeracao
			       AND pagamento.cod_convenio = carne.cod_convenio
                        ) AS apag
                        ON apag.cod_parcela = carne.cod_parcela
                        
			LEFT JOIN 	arrecadacao.carne_devolucao as carned
			ON carned.numeracao = carne.numeracao and carned.cod_convenio = carne.cod_convenio
			
		WHERE
			--carned.numeracao is null
            case when carned.numeracao is not null then
                case when 1 < (select count(*) from arrecadacao.carne_devolucao acd where acd.numeracao = carne.numeracao
                                                                                 and acd.cod_convenio = carne.cod_convenio) then
                    false
                else
                    carned.cod_motivo = 10
                end
            else
                true 
            end
			--and apag.numeracao is null
                        AND apag.cod_parcela IS NULL
			and ap.nr_parcela > 0
			and	ap.cod_lancamento = ''||inCodLancamento||''


        GROUP BY

            carne.exercicio
            , carne.cod_convenio
            , ap.cod_parcela
            , alc.cod_calculo
            , alc.valor
            , calc.cod_credito
            , mon.descricao_credito
            , mon.cod_natureza
            , mon.cod_genero
            , mon.cod_especie
		ORDER BY
            ap.cod_parcela, alc.cod_calculo '';

END IF;

    FOR reRecord IN EXECUTE stSql LOOP
		return next reRecord;
    END LOOP;
	return;

END;
' LANGUAGE 'plpgsql';
