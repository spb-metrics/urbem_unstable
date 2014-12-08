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
CREATE OR REPLACE FUNCTION tcemg.especifPrev(stExercicio varchar, dtInicio varchar, dtFinal varchar, stEntidades varchar, stRpps varchar) returns RECORD AS $$
DECLARE
	stSql varchar;
	reRegistro RECORD;
BEGIN
	stSql := 'SELECT  stn.pl_saldo_contas (   '|| quote_literal(stExercicio) ||'
                            , '|| quote_literal(dtInicio) ||'
                            , '|| quote_literal(dtFinal) ||'
                            , plano_conta.cod_estrutural like ''1.1.1.1.3%'' 
                            , '|| quote_literal(stEntidades) ||'
                            , '|| quote_literal(stRpps) ||'
		    ) as aplicacoes_financeiras
			,      stn.pl_saldo_contas (     '|| quote_literal(stExercicio) ||'
                            , '|| quote_literal(dtInicio) ||'
                            , '|| quote_literal(dtFinal) ||'
                            , plano_conta.cod_estrutural like ''1.1.1.1.1%''
                            , '|| quote_literal(stEntidades) ||'
                            , '|| quote_literal(stRpps) ||'
		    ) as caixa 
	
			,  stn.pl_saldo_contas (      '|| quote_literal(stExercicio) ||'
                            , '|| quote_literal(dtInicio) ||'
                            , '|| quote_literal(dtFinal) ||'
                            , plano_conta.cod_estrutural like ''1.1.1.1.2%'' AND recurso_direto.tipo = ''L''
                            , '|| quote_literal(stEntidades) ||'
                            , '|| quote_literal(stRpps) ||'
		    ) as conta_movimento 

		,     stn.pl_saldo_contas (      '|| quote_literal(stExercicio) ||'
                            , '|| quote_literal(dtInicio) ||'
                            , '|| quote_literal(dtFinal) ||'
                            ,  plano_conta.cod_estrutural like ''1.1.1.1.2%'' AND recurso_direto.tipo = ''V''
                            , '|| quote_literal(stEntidades) ||'
                            , '|| quote_literal(stRpps) ||'
		    ) as contas_vinculadas';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN reRegistro;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
