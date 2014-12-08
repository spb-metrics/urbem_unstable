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
* $Revision:$
* $Name$
* $Author:$
* $Date:$
*
* Casos de uso: 
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_gerar_restos_pagar_individual(
	stExercicio    varchar,
	inEntidade     integer,
	stCodEstDeb    varchar,
	stCodEstCred   varchar,
	nuValor_rp     numeric(12,2),
	stComplemento  varchar) RETURNS INTEGER AS $$
DECLARE
	inCodLote  INTEGER;
	sequencia  INTEGER;
BEGIN
	SELECT cod_lote INTO inCodLote
	  FROM contabilidade.lote
	 WHERE exercicio    = stExercicio
	   AND cod_entidade = inEntidade
	   AND tipo 	    = 'M'
           AND nom_lote     ilike 'ENCERRAMENTO%';

	IF inCodLote IS NULL THEN
            inCodLote := contabilidade.fn_insere_lote( stExercicio::varchar    	   -- stExercicio
                                                     , inEntidade::integer         -- inCodEntidade
                                                     , 'M'                         -- stTipo
                                                     , 'ENCERRAMENTO DO EXERCÍCIO' -- stNomeLote
                                                     , '31/12/'||stExercicio       -- stDataLote
                                                     );
	END IF;

        sequencia := FazerLancamento(stCodEstDeb,stCodEstCred,800,stExercicio,abs(nuValor_rp), stComplemento,inCodLote::integer,'M'::varchar,inEntidade::integer);
	RETURN sequencia;
END;
$$LANGUAGE 'plpgsql';
