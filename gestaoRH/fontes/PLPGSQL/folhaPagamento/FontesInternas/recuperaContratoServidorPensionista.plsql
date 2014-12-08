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
--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/10/04 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o registro e retorna o codigo do contrato
--



CREATE OR REPLACE FUNCTION recuperaContratoServidorPensionista(integer) RETURNS integer as $$

DECLARE
    inCodContrato                   ALIAS FOR $1;
    inCodContratoGeradorBeneficio   INTEGER := 0;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN

    inCodContratoGeradorBeneficio := pega0ContratoDoGeradorBeneficio(inCodContrato);
    IF inCodContratoGeradorBeneficio IS NOT NULL THEN
        --Retorno o cod_contrato do servidor que gerou o benefício para o pensionista
        --Isso acontece quando o cod_contrato recebido como parâmetro é o cod_contrato de um pensionista
        RETURN inCodContratoGeradorBeneficio;
    ELSE
        --Retorno o cod_contrato recebido como parâmetro
        --Isso acontece quando o cod_contrato recebido como parâmetro é o cod_contrato de um servidor
        RETURN inCodContrato;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

