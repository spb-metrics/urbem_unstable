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
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_empenho_total_pago(varchar,integer,integer,varchar) RETURNS NUMERIC(14,2)  AS '
DECLARE
    stExercicio      ALIAS FOR $1            ;
    inCodEmpenho     ALIAS FOR $2            ;
    inCodEntidade    ALIAS FOR $3            ;
    stExercicioAtual ALIAS FOR $4            ;
    nuSoma           NUMERIC(14,2)   := 0.00 ;

BEGIN
    SELECT  coalesce(Sum(enlp.vl_pago),0.00)
    INTO    nuSoma
    FROM    empenho.nota_liquidacao_paga    as enlp,
            empenho.nota_liquidacao         as enl,
            empenho.empenho                 as ee
    WHERE   ee.exercicio            =   stExercicio
        AND ee.cod_empenho          =   inCodEmpenho
        AND ee.cod_entidade         =   inCodEntidade
      -- Nota Liquidacao
        AND enl.exercicio_empenho   =   ee.exercicio
        AND enl.cod_empenho         =   ee.cod_empenho
        AND enl.cod_entidade        =   ee.cod_entidade
      -- Nota Liquidacao Paga
        AND enlp.cod_entidade       =   enl.cod_entidade
        AND enlp.cod_nota           =   enl.cod_nota
        AND enlp.exercicio          =   enl.exercicio
        AND to_date(enlp.timestamp,''yyyy-mm-dd'') <= to_date(''31/12/''||to_number(stExercicioAtual,''9999'')-1,''dd/mm/yyyy'')
;

    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
