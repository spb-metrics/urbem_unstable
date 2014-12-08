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
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23830 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/04/24 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e a data e retorna 
-- a quantidade de dependentes para imposto de renda registrados nos atributos dinamicos
--/
--



CREATE OR REPLACE FUNCTION pega1QtdDependentesSalarioFamiliaAtributoDinamico() RETURNS integer as '

DECLARE
    --inCodContrato                    ALIAS FOR $1;
    --stTimestamp                      ALIAS FOR $2;
    inCodContrato                    INTEGER;

    inQtdDependentesSalarioFamilia   INTEGER := 0;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodContrato := recuperarBufferInteiro( ''inCodContrato'' );
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    inQtdDependentesSalarioFamilia  := selectIntoInteger(''
        SELECT to_number( valor, 99 ) as valor 
          FROM pessoal''||stEntidade||''.atributo_contrato_servidor_valor as p 

          LEFT OUTER JOIN administracao.atributo_dinamico as ad
            ON ad.cod_modulo=22
           AND ad.cod_cadastro=5
           AND nom_atributo = ''''Dependentes Salario Familia''''
           AND ad.ativo = ''''t''''
           AND p.cod_atributo = ad.cod_atributo

         WHERE cod_contrato= ''||inCodContrato||''
           --AND timestamp <= stTimestamp
           AND ad.cod_atributo is not null
        ORDER BY timestamp desc LIMIT 1 ''
                  );

    RETURN inQtdDependentesSalarioFamilia;
END;
' LANGUAGE 'plpgsql';

