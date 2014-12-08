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
/* tr_situacao_contrato_aposentadoria_excluida
*
* Data de Criacao : 25/09/2013

* @author Analista : Dagiane
* @author Desenvolvedor : Fabio e Schitz

* @package URBEM
* @subpackage

$Id:$
*/

CREATE OR REPLACE FUNCTION tr_situacao_contrato_aposentadoria_excluida() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        stSQL := '
                   DELETE FROM '|| stSchema ||'.contrato_servidor_situacao
                    WHERE cod_contrato = '|| NEW.cod_contrato   ||'
                      AND situacao     = '|| quote_literal('P') ||'
                        ;
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';