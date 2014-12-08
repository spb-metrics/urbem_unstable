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
CREATE OR REPLACE FUNCTION orcamentosuplementacoescreditoespecial (character varying, numeric, character varying, integer, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    Exercicio           ALIAS FOR $1;
    Valor               ALIAS FOR $2;
    Complemento         ALIAS FOR $3;
    CodLote             ALIAS FOR $4;
    TipoLote            ALIAS FOR $5;
    CodEntidade         ALIAS FOR $6;
    CredSuplementar     ALIAS FOR $7;

    Sequencia           INTEGER;
    TipoCredSuplementar VARCHAR := '';
BEGIN
    TipoCredSuplementar := CredSuplementar;
    IF Exercicio::integer > 2013 THEN
        IF TipoCredSuplementar = 'Reducao' THEN
            Sequencia := FazerLancamento(  '622110000' , '522190109' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522120201' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522130300' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '52212020100' , '622110000' , 908 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '52213040000' , '522139900' , 908 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TipoCredSuplementar = 'Auxilios' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213020203' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Excesso' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213020000' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Superavit' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213010000' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Especial Reaberto' THEN
            Sequencia := FazerLancamento(  '522120202' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    ELSIF EXERCICIO::integer = 2013 THEN
        IF TipoCredSuplementar = 'Reducao' THEN
            Sequencia := FazerLancamento(  '622110000' , '522190109' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522120201' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522130204' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '52212020100' , '622110000' , 908 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '52213020300' , '522139900' , 908 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TipoCredSuplementar = 'Auxilios' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213020203' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Excesso' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213020201' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Superavit' THEN
            Sequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '52213020100' , '522139900' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Especial Reaberto' THEN
            Sequencia := FazerLancamento(  '522120202' , '622110000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    ELSE
        IF TipoCredSuplementar = 'Reducao' THEN
            Sequencia := FazerLancamento(  '192130100020000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292110000000000' , '192190209000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Excesso' THEN
            Sequencia := FazerLancamento(  '192130100030000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '191110000000000' , '291120000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Operacao de Credito' THEN
            Sequencia := FazerLancamento(  '192130100040000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Superavit' THEN
            Sequencia := FazerLancamento(  '192130101010000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Doacoes' THEN
            Sequencia := FazerLancamento(  '192130100060000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Auxilios' THEN
            Sequencia := FazerLancamento(  '192130100050000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF TipoCredSuplementar = 'Especial Reaberto' THEN
            Sequencia := FazerLancamento(  '192130200000000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    END IF;

    RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';
