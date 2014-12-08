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
     * Classe de mapeamento para a tabela IMOBILIARIO.vw_area_lote_atual
     * Data de Criação: 02/01/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerir

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCIMAreaLoteAtual.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a view  IMOBILIARIO.vw_area_lote_atual
  * Data de Criação: 02/01/2005

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCIMAreaLoteAtual extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCIMAreaLoteAtual()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_area_lote_atual');

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('cod_grandeza','integer',true,'',true,false);
    $this->AddCampo('cod_unidade','integer',true,'',true,false);
    $this->AddCampo('area_real','numeric',true,'14,2',false,false);
}
}
