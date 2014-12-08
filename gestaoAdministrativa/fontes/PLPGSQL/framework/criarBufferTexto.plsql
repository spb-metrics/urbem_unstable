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
 * Titulo do arquivo : criarBufferTexto
 * Data de Criação   : 01/04/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
--##BUFFER TEXTO  #######################################################################
--#######################################################################################
--Cria um buffer texto na tabela administracao.buffers_texto
create or replace function criarBufferTexto(varchar,varchar) returns varchar as $$
declare
    stBufferPar alias for $1;
    stValor     alias for $2;
    stBuffer    varchar;
begin
    stBuffer := LOWER(TRIM(stBufferPar));

    PERFORM 1
       FROM administracao.buffers_texto
      WHERE buffer = stBuffer
          ;
    IF FOUND THEN
        UPDATE administracao.buffers_texto
           SET valor  = stValor
         WHERE buffer = stBuffer
             ;
    ELSE
        INSERT
          INTO administracao.buffers_texto
             ( buffer
             , valor
             )
        VALUES
             ( stBuffer
             , stValor
             );
    END IF;
    RETURN stValor;
end;
$$ language 'plpgsql';
