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
* $Id: fn_acrescimo_indice.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.00
* Calculo Valor de Juros Especial (Mata)
*/

CREATE OR REPLACE FUNCTION fn_correcao_mariana (date,date,numeric,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        nuValor         ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        nuJuros         NUMERIC = 0.00;
        nuRetorno       NUMERIC = 0.00;
        inDiff          INTEGER;
        nuValorVenc     numeric = 0.0;
        nuValorPag      numeric = 0.0;
        nuJuroTotal     numeric = 0.0;
        inMesInicio     integer;
        inMesFim        integer;
        inAno           integer;
        inTeste         integer;
        inTotalMes      INTEGER;

    BEGIN
       -- Calculo de Juros simples                                                                            
        nuJuroTotal := 0.00;
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

        inMesInicio := date_part(''month'' , dtVencimento )::integer + 1;
        inMesFim := date_part(''month'' , dtVencimento )::integer + ( inDiff );

        inAno := date_part(''year'' , dtVencimento )::integer;

        inTotalMes := inMesInicio;

        IF ( inDiff > 0 ) THEN

            SELECT valor
              INTO nuValorVenc
              FROM monetario.valor_acrescimo
             WHERE valor_acrescimo.cod_acrescimo = inCodAcrescimo
               AND valor_acrescimo.cod_tipo = inCodTipo
               AND inicio_vigencia = (
                                       SELECT MAX(inicio_vigencia)
                                         FROM monetario.valor_acrescimo 
                                        WHERE cod_acrescimo    = inCodAcrescimo
                                          AND cod_tipo         = inCodTipo
                                          AND inicio_vigencia <= dtVencimento
                                     );

            SELECT valor
              INTO nuValorPag
              FROM monetario.valor_acrescimo
             WHERE valor_acrescimo.cod_acrescimo = inCodAcrescimo
               AND valor_acrescimo.cod_tipo = inCodTipo
               AND inicio_vigencia = (
                                       SELECT MAX(inicio_vigencia)
                                         FROM monetario.valor_acrescimo 
                                        WHERE cod_acrescimo    = inCodAcrescimo
                                          AND cod_tipo         = inCodTipo
                                          AND inicio_vigencia <= dtDataCalculo
                                     );

            nuRetorno      := nuValor / nuValorVenc * nuValorPag - nuValor ; 

        END IF;

        RETURN nuRetorno::numeric(14,2);
    END;
'language 'plpgsql';
           
