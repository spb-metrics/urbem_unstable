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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_pago_recurso(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,CHAR(1)) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    stCodEntidade              ALIAS FOR $2;
    inCodRecurso               ALIAS FOR $3;
    stDtInicial                ALIAS FOR $4;
    stDtFinal                  ALIAS FOR $5;
    stRestos                   ALIAS FOR $6;
    stSql                      VARCHAR := '''';
    crCursor                   REFCURSOR;
    nuValor                    NUMERIC := 0;

BEGIN

     stSql := ''SELECT
                    coalesce(sum(NLP.vl_pago),0.00)
                FROM    orcamento.despesa            AS OD
                       ,empenho.pre_empenho_despesa  AS EPED
                       ,empenho.pre_empenho          AS EPE
                       ,empenho.empenho              AS EE
                       ,empenho.nota_liquidacao      AS NL
                       ,empenho.nota_liquidacao_paga AS NLP
                WHERE   NLP.cod_nota         = NL.cod_nota
                AND     NLP.exercicio        = NL.exercicio
                AND     NLP.cod_entidade     = NL.cod_entidade
                AND     NL.exercicio_empenho = EE.exercicio
                AND     NL.cod_entidade      = EE.cod_entidade
                AND     NL.cod_empenho       = EE.cod_empenho
                AND     EE.cod_pre_empenho   = EPE.cod_pre_empenho
                AND     EE.exercicio         = EPE.exercicio
                AND     EPE.cod_pre_empenho  = EPED.cod_pre_empenho
                AND     EPE.exercicio        = EPED.exercicio
                AND     EPED.cod_despesa     = OD.cod_despesa
                AND     EPED.exercicio       = OD.exercicio
                AND     OD.cod_recurso       = '' || inCodRecurso || ''
    '';

    IF ( stRestos = ''R'' ) then
        stSql := stSql || ''
                AND     EE.exercicio         < '''''' || stExercicio || ''''''
        '';
    ELSE
        stSql := stSql || ''
                AND     EE.exercicio         = '''''' || stExercicio || ''''''
        '';
    END IF;
    stSql := stSql || ''
                AND     OD.cod_entidade    IN ( '' || stCodEntidade || '' )
                AND     TO_DATE( NLP.timestamp, ''''yyyy-mm-dd'''' ) BETWEEN
                                    TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                AND TO_DATE( '''''' || stDtFinal   || '''''', ''''dd/mm/yyyy'''' )
    '';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuValor;
    CLOSE crCursor;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;

END;
'LANGUAGE 'plpgsql';
