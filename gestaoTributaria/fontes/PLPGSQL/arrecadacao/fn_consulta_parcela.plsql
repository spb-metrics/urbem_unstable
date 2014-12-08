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
* $Id: fn_consulta_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.15  2007/04/13 17:55:41  dibueno
Raise's comentados

Revision 1.14  2007/03/12 21:25:18  dibueno
*** empty log message ***

Revision 1.13  2007/01/26 15:43:05  dibueno
concatenação dos campos "numeracao_migracao"/"prefixo"

Revision 1.12  2006/11/21 16:10:11  dibueno
Melhoria no SQL. Identação

Revision 1.11  2006/11/06 16:33:46  dibueno
Bug #7351#

Revision 1.10  2006/10/25 11:22:49  dibueno
Caso a parecela seja reemitida, deve-se exibir o valor original como valor correto

Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_parcela (integer,date) returns SETOF RECORD AS '
DECLARE
   inCodLancamento      ALIAS FOR $1;
   dtDataBase           ALIAS FOR $2;
   stSql                VARCHAR;
   stSql2               VARCHAR;
   reRecord1            RECORD;
   reRecord2            RECORD;
BEGIN
    stSql = ''
        SELECT
            al.cod_lancamento::integer
            , ap.cod_parcela::integer
            , ap.nr_parcela::integer

            , ( case when apr.cod_parcela is not null then
                    apr.valor
                else
                    ap.valor
                end
            )::numeric(14,2) as valor
            , (to_char(ap.vencimento,''''dd/mm/YYYY''''))::varchar as vencimento
            , ''''''''::varchar as vencimento_original
            , ap.vencimento as vencimento_us
            , ( case when ap.nr_parcela = 0
                then ''''Única''''::VARCHAR
              else
                (ap.nr_parcela::varchar||''''/''''|| arrecadacao.fn_total_parcelas(al.cod_lancamento))::varchar
              end
            ) as info_parcela

            , now()::date as database

            , (to_char(now()::date, ''''dd/mm/YYYY''''))::varchar as database_br,
            ''''''''::varchar as numeracao,
            ''''''''::varchar as exercicio,
            ''''''''::varchar as situacao,
            ''''''''::varchar as situacao_resumida,
            ''''''''::varchar as numeracao_migracao,
            ''''''''::varchar as prefixo,
            now()::date  as pagamento,
            0::integer as ocorrencia_pagamento
        FROM
            arrecadacao.lancamento al 
            INNER JOIN (
				select
					cod_lancamento, cod_parcela, nr_parcela, valor,
					arrecadacao.fn_atualiza_data_vencimento( vencimento ) as vencimento
				from arrecadacao.parcela
			) as ap ON al.cod_lancamento   = ap.cod_lancamento

			LEFT JOIN
			(
				select apr.cod_parcela, vencimento, valor
				from arrecadacao.parcela_reemissao apr
				inner join (
					select cod_parcela, min(timestamp) as timestamp
					from arrecadacao.parcela_reemissao as x
					group by cod_parcela
				) as apr2
				ON apr2.cod_parcela = apr.cod_parcela AND
				apr2.timestamp = apr.timestamp
			) as apr
			ON apr.cod_parcela = ap.cod_parcela
        WHERE
            al.cod_lancamento=''||inCodLancamento||''
        ORDER BY 
            ap.cod_parcela
        '';

    FOR reRecord1 IN EXECUTE stSql LOOP
        stSql2 := ''
            SELECT
                *
            FROM
                arrecadacao.fn_consulta_numeracao_parcela (''||reRecord1.cod_parcela||'',''''''||dtDataBase||'''''')
            as ( numeracao varchar, exercicio varchar, situacao varchar, situacao_resumida varchar, numeracao_migracao varchar, prefixo varchar, vencimento_original varchar, pagamento date, ocorrencia_pagamento int )
        '';


      FOR reRecord2 IN EXECUTE stSql2 LOOP
           reRecord1.numeracao          	:= reRecord2.numeracao;
           reRecord1.exercicio              := reRecord2.exercicio;
           reRecord1.situacao              	:= reRecord2.situacao ;
           reRecord1.situacao_resumida     	:= reRecord2.situacao_resumida;
           reRecord1.numeracao_migracao 	:= reRecord2.numeracao_migracao||''/''||reRecord2.prefixo;
           reRecord1.prefixo                := reRecord2.prefixo ;
           reRecord1.pagamento              := reRecord2.pagamento;
           reRecord1.vencimento_original    := reRecord2.vencimento_original;
           reRecord1.ocorrencia_pagamento   := reRecord2.ocorrencia_pagamento;
      END LOOP;
      return next reRecord1;
    END LOOP;

    return;
END;
' LANGUAGE 'plpgsql';
