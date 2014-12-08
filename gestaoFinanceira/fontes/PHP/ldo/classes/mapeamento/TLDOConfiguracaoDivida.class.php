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
    * Mapeamento da tabela ldo.configuracao_divida
    * Data de Criação   : 08/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

class TLDOConfiguracaoDivida extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.configuracao_divida');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_ppa,ano,cod_tipo,exercicio');

        $this->addCampo('cod_ppa'  , 'integer'  , true, ''    , true , true);
        $this->addCampo('ano'      , 'character', true, '1'   , true , true);
        $this->addCampo('cod_tipo' , 'integer'  , true, ''    , true , true);
        $this->addCampo('exercicio', 'character', true, '4'   , true , false);
        $this->addCampo('valor'    , 'numeric'  , true, '14,2', false, false);

    }
}

?>
