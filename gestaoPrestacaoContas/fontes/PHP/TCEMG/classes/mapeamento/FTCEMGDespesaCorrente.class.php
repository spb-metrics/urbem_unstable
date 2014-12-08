<?php
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
?>
<?php
/**
    * Arquivo de mapeamento para a função que busca os dados de despesa corrente
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaCorrente()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_despesa_corrente');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
    SELECT mes
          ,despPesEncSoc
          ,despJurDivInt
          ,despJurDivExt
          ,despOutDespCor
          ,mesanula
          ,despPesEncSocanula
          ,despJurDivIntanula
          ,despJurDivExtanula
          ,despOutDespCoranula
          ,mesatual
          ,despPesEncSocatual
          ,despJurDivIntatual
          ,despJurDivExtatual
          ,despOutDespCoratual
          ,mesliqui
          ,despPesEncSocliqui
          ,despJurDivIntliqui
          ,despJurDivExtliqui
          ,despOutDespCorliqui
          ,mesatualizada
          ,despPesEncSocatualizada
          ,despJurDivIntatualizada
          ,despJurDivExtatualizada
          ,despOutDespCoratualizada
          ,mesemp
          ,despPesEncSocemp
          ,despJurDivIntemp
          ,despJurDivExtemp
          ,despOutDespCoremp
          ,codTipoini
          ,codTipoanula
          ,codTipoliqui
          ,codTipoemp
          ,codTipoatual
          ,codTipoatualizada
          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'
                                     , ".$this->getDado("mes")."
                                     ) AS retorno(
                                                  mes                  INTEGER,
                                                  despPesEncSoc        NUMERIC(14,2),
                                                  despJurDivInt        Numeric(14,2),
                                                  despJurDivExt        Numeric(14,2),
                                                  despOutDespCor       NUMERIC(14,2),
                                                  mesanula             INTEGER,
                                                  despPesEncSocanula   Numeric(14,2),
                                                  despJurDivIntanula   Numeric(14,2),
                                                  despJurDivExtanula   Numeric(14,2),
                                                  despOutDespCoranula  NUMERIC(14,2),
                                                  mesatual             INTEGER,
                                                  despPesEncSocatual   Numeric(14,2),
                                                  despJurDivIntatual   Numeric(14,2),
                                                  despJurDivExtatual   Numeric(14,2),
                                                  despOutDespCoratual  NUMERIC(14,2),
                                                  mesliqui             INTEGER,
                                                  despPesEncSocliqui Numeric(14,2),
                                                  despJurDivIntliqui Numeric(14,2),
                                                  despJurDivExtliqui Numeric(14,2),
                                                  despOutDespCorliqui NUMERIC(14,2),
                                                  mesatualizada INTEGER,
                                                  despPesEncSocatualizada Numeric(14,2),
                                                  despJurDivIntatualizada Numeric(14,2),
                                                  despJurDivExtatualizada Numeric(14,2),
                                                  despOutDespCoratualizada NUMERIC(14,2),
                                                  mesemp INTEGER,
                                                  despPesEncSocemp Numeric(14,2),
                                                  despJurDivIntemp Numeric(14,2),
                                                  despJurDivExtemp Numeric(14,2),
                                                  despOutDespCoremp NUMERIC(14,2),
                                                  codTipoini INTEGER,
                                                  codTipoanula INTEGER,
                                                  codTipoliqui INTEGER,
                                                  codTipoemp INTEGER,
                                                  codTipoatual INTEGER,
                                                  codTipoatualizada INTEGER
                                                 )";
return $stSql;
}

}
